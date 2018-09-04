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

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMSSerializer;

/**
 * Class CharacterEntity.
 *
 * @ORM\Entity
 * @ORM\Table(name="`recruitment`")
 *
 * @JMSSerializer\ExclusionPolicy("all")
 */
class RecruitmentEntity
{

    /**
     * The id of the recruitment.
     *
     * @var int $id
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("integer")
     */
    private $id;

    /**
     * The user this recruitment belongs to.
     *
     * @var UserEntity $user
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\UserEntity", inversedBy="recruitments")
     * @ORM\JoinColumn(nullable=false)
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("integer")
     * @JMSSerializer\Accessor(getter="getUserId")
     */
    private $user;

    /**
     * The form data of the recruitment.
     *
     * @var array $form
     *
     * @ORM\Column(type="json_array")
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("array")
     */
    private $form;

    /**
     * Get the id of the recruitment.
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the user where this recruitment belongs to.
     *
     * @return UserEntity
     */
    public function getUser(): UserEntity
    {
        return $this->user;
    }

    /**
     * Get the user id where this recruitment belongs to.
     *
     * @return integer
     */
    public function getUserId(): int
    {
        return $this->getUser()->getId();
    }

    /**
     * Set the user this user belongs to.
     *
     * @param UserEntity $user The user.
     *
     * @return void
     */
    public function setUser(UserEntity $user): void
    {
        $this->user = $user;
    }

    /**
     * Get the form of this recruitment.
     *
     * @return array
     */
    public function getForm(): array
    {
        return $this->form;
    }

    /**
     * Set the form of this recruitment.
     *
     * @param array $form The recruitment form.
     *
     * @return void
     */
    public function setForm(array $form): void
    {
        $this->form = $form;
    }

}