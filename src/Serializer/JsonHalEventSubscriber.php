<?php declare (strict_types=1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Serializer;

use App\Annotation\HrefLink;
use App\Annotation\Linked;
use App\Exception\NonExisitingMethodException;
use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use ReflectionClass;

class JsonHalEventSubscriber implements EventSubscriberInterface
{

    /**
     * The doctrine annotion reader.
     *
     * @var AnnotationReader $reader
     */
    private $reader;

    /**
     * The object to serialize.
     *
     * @var mixed $object
     */
    private $object;

    /**
     * The reflected object.
     *
     * @var ReflectionClass $reflectedObject
     */
    private $reflectedObject;

    /**
     * The visitor.
     *
     * @var SerializationVisitorInterface $visitor
     */
    private $visitor;

    /**
     * The links data
     *
     * @var ArrayCollection
     */
    private $linksData;

    /**
     * The links.
     *
     * @var StaticPropertyMetadata $links
     */
    private $links;

    /**
     * The subscribed event.
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return array(
            array(
                'event' => 'serializer.post_serialize',
                'method' => 'onPreSerialize',
                'format' => 'json',
                'priority' => 0,
            ),
        );
    }

    /**
     * Runs on post serialize event.
     *
     * @param ObjectEvent $event The event.
     *
     * @return ObjectEvent
     *
     * @throws AnnotationException                   When the dcotrine reader fails to initialize.
     * @throws \ReflectionException                  When the class could not be reflected.
     * @throws NonExisitingMethodException When the identifier method does not exist.
     */
    public function onPreSerialize(ObjectEvent $event): ObjectEvent
    {
        $this->initializePreSerialize($event);

        if ($this->hasHrefLink() === false) {
            return $event;
        }

        $this->setSelfLink();
        $this->setLinks();

        $this->visitor->visitProperty(
            $this->links,
            $this->linksData
        );

        return $event;
    }

    /**
     * Initialize the subscriber to serialize the data.
     *
     * @param ObjectEvent $event The event.
     *
     * @throws AnnotationException  When the doctrine reader fails to initialize.
     * @throws \ReflectionException When the class could not be reflected.
     *
     * @return void
     */
    private function initializePreSerialize(ObjectEvent $event): void
    {
        $this->reader = new AnnotationReader();
        $this->object = $event->getObject();
        $this->reflectedObject = new \ReflectionClass($this->object);
        $this->visitor = $event->getVisitor();
        $this->linksData = new ArrayCollection();
        $this->links = new StaticPropertyMetadata('', '_links', $this->linksData);
    }

    /**
     * Checks if the HrefLink is present for the object to be serialized.
     *
     * @return bool
     */
    private function hasHrefLink(): bool
    {
        return $this->reader->getClassAnnotation($this->reflectedObject, HrefLink::class) !== null;
    }

    /**
     * Set the href link.
     *
     * @return void
     *
     * @throws NonExisitingMethodException When the identifier method does not exist.
     */
    private function setSelfLink(): void
    {
        $classAnnotation = $this->reader->getClassAnnotation($this->reflectedObject, HrefLink::class);

        // This can never happen but just to keep things sane.
        if ($classAnnotation === null) {
            return;
        }

        $this->checkMethodExist($this->reflectedObject, $classAnnotation->identifier);
        $this->linksData->set('self', [
            'href' => sprintf(
                '%s/%s',
                $classAnnotation->href,
                $this->object->{$classAnnotation->identifier}()
            ),
        ]);
    }

    /**
     * Set the links.
     *
     * @return void
     *
     * @throws \ReflectionException        When the class could not be reflected.
     * @throws NonExisitingMethodException When the identifier or accessor method do not exist.
     */
    private function setLinks(): void
    {
        foreach ($this->reflectedObject->getProperties() as $reflectionProperty) {
            $propertyAnnotation = $this->reader->getPropertyAnnotation($reflectionProperty, Linked::class);

            if ($propertyAnnotation === null) {
                continue;
            }

            $this->checkMethodExist($this->reflectedObject, $propertyAnnotation->accessor, 'accessor');

            $hrefs = [];
            // @todo non iterable href links
            if (is_iterable($this->object->{$propertyAnnotation->accessor}()) === true) {
                foreach ($this->object->{$propertyAnnotation->accessor}() as $object) {
                    $this->checkMethodExist(new \ReflectionClass($object), $propertyAnnotation->identifier);
                    $hrefs[] = [
                        'href' => sprintf(
                            '%s/%s',
                            $propertyAnnotation->href,
                            $object->{$propertyAnnotation->identifier}()
                        ),
                    ];
                }
            }

            $this->linksData->set($reflectionProperty->getName(), $hrefs);
        }
    }

    /**
     * Checks if the method exists.
     *
     * @param ReflectionClass $class  The refected class.
     * @param string          $method The method.
     * @param string          $type   The type.
     *
     * @return void
     *
     * @throws NonExisitingMethodException When the method does not exist.
     */
    private function checkMethodExist(ReflectionClass $class, string $method, string $type = 'identifier'): void
    {
        if ($class->hasMethod($method) === false) {
            throw new NonExisitingMethodException(
                sprintf(
                    'The %s "%s" does not exist in class: "%s"',
                    $type,
                    $method,
                    $class->getShortName()
                )
            );
        }
    }

}
