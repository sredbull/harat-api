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
use OpenApi\Annotations as OA;

/**
 * Class GroupEntity.
 *
 * @ORM\Entity
 * @ORM\Table(name="groups")
 *
 * @OA\Schema(schema="GroupEntity")
 */
class GroupEntity implements EntityInterface
{

    /**
     * The id of the group.
     *
     * @var int $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The group name.
     *
     * @var string $name
     *
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @OA\Property()
     */
    private $name;

    /**
     * The roles.
     *
     * @var array $roles
     *
     * @ORM\Column(type="array")
     *
     * @OA\Property(@OA\Items(type="string"))
     */
    private $roles;

    /**
     * Group constructor.
     *
     * @param string $name  The name of the group.
     * @param array  $roles The roles of the group.
     */
    public function __construct(string $name, array $roles = [])
    {
        $this->name = $name;
        $this->roles = $roles;
    }

    /**
     * Get the id of the group.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the name of the group
     *
     * @param int $id The id.
     *
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the name of the group
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the name of the group.
     *
     * @param string $name The name.
     *
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the roles of the group.
     *
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Set the roles of the group.
     *
     * @param array $roles The roles.
     *
     * @return void
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

}
