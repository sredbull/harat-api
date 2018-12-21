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
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Metadata\StaticPropertyMetadata;

class JsonHalEventSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
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

    public function onPreSerialize(ObjectEvent $event)
    {
        if ($this->hasHrefLink($event->getObject()) === true) {
            return;
        }

        $this->setHrefLink($event->getVisitor());

        return $event;
    }

    private function hasHrefLink($object)
    {
        $reader = new AnnotationReader();
        $hrefLink = $reader->getClassAnnotation(
            new \ReflectionClass($object),
            HrefLink::class
        );

        return $hrefLink === null;
    }

    private function setHrefLink(JsonSerializationVisitor $visitor)
    {
        $links = new StaticPropertyMetadata('', '_links', []);
        $visitor->visitProperty(
            $links,
            []
        );
    }

}
