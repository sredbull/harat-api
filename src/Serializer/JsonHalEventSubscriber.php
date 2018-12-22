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
use App\Exception\NonExisitingIdentifierMethodException;
use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
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
     * @throws NonExisitingIdentifierMethodException When the identifier method does not exist.
     */
    public function onPreSerialize(ObjectEvent $event): ObjectEvent
    {
        $this->initializePreSerialize($event);

        if ($this->hasHrefLink() === false) {
            return $event;
        }

        $this->setHrefSelfLink();

        return $event;
    }

    /**
     * Initialize the subscriber to serialize the datta.
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
     * @throws NonExisitingIdentifierMethodException When the identifier method does not exist.
     */
    private function setHrefSelfLink(): void
    {
        $classAnnotation = $this->reader->getClassAnnotation($this->reflectedObject, HrefLink::class);

        // This can never happen but just to keep things sane.
        if ($classAnnotation === null) {
            return;
        }

        if ($this->reflectedObject->hasMethod($classAnnotation->identifier) === false) {
            throw new NonExisitingIdentifierMethodException(
                sprintf(
                    'The identifier "%s" does not exist in class: "%s"',
                    $classAnnotation->identifier,
                    \get_class($this->object)
                )
            );
        }

        $links = new StaticPropertyMetadata('', '_links', []);
        $this->visitor->visitProperty(
            $links,
            [
                'self' => [
                    'href' => sprintf(
                        '%s/%s',
                        $classAnnotation->href,
                        $this->object->{$classAnnotation->identifier}()
                    ),
                ],
            ]
        );
    }

}
