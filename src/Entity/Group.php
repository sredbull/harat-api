<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\Group as BaseGroup;
use FOS\UserBundle\Model\GroupInterface;
use JMS\Serializer\Annotation as JMSSerializer;

/**
 * Class Group.
 *
 * @ORM\Entity
 * @ORM\Table(name="groups")
 * @package App\Entity
 *
 * @JMSSerializer\ExclusionPolicy("all")
 */
class Group extends BaseGroup implements GroupInterface
{
    /**
     * The id of the group.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("integer")
     */
    protected $id;
}
