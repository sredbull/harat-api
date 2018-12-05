<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright House Aratus
 */
namespace App\Entity;

use App\Interfaces\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\Group as BaseGroup;
use JMS\Serializer\Annotation as JMSSerializer;

/**
 * Class GroupEntity.
 *
 * @ORM\Entity
 * @ORM\Table(name="groups")
 *
 * @JMSSerializer\ExclusionPolicy("all")
 *
 * @OA\Schema(schema="GroupEntity")
 */
class GroupEntity extends BaseGroup implements EntityInterface
{

    /**
     * The id of the group.
     *
     * @var int $id
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
