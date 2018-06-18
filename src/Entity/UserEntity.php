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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation as JMSSerializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserEntity.
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 *
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 *
 * @JMSSerializer\ExclusionPolicy("all")
 */
class UserEntity extends BaseUser implements UserInterface
{

    /**
     * The id of the user.
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

    /**
     * The name of the user.
     *
     * @var string $username
     *
     * @Assert\NotBlank()
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     */
    protected $username;

    /**
     * The email of the user.
     *
     * @var string $email
     *
     * @Assert\Email(
     *     strict = true,
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true,
     *     checkHost = true
     * )
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     */
    protected $email;

    /**
     * The user groups.
     *
     * @var GroupInterface[]|Collection $groups
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\GroupEntity")
     * @ORM\JoinTable(name="user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("array")
     */
    protected $groups;

    /**
     * The avatar url of the member.
     *
     * @var string $avatar
     *
     * @ORM\Column(type="string", length=2048, nullable=true)
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     */
    private $avatar;

    /**
     * The characters of this user.
     *
     * @var string $character
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CharacterEntity", mappedBy="user", cascade={"persist"})
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("array")
     */
    private $characters;

    /**
     * UserEntity constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->characters = new ArrayCollection();
    }

    /**
     * Get the avatar.
     *
     * @return null|string
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * Set the avatar.
     *
     * @param null|string $avatar The url of the avatar.
     *
     * @return void
     */
    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * Get the characters for this user.
     *
     * @return Collection|CharacterEntity[]
     */
    public function getCharacters(): Collection
    {
        return $this->characters;
    }

    /**
     * Add a character to this user.
     *
     * @param CharacterEntity $character The character to be added.
     *
     * @return void
     */
    public function addCharacter(CharacterEntity $character): void
    {
        if (!$this->characters->contains($character)) {
            $this->characters[] = $character;
            $character->setUserId($this);
        }
    }

    /**
     * Removes a character.
     *
     * @param CharacterEntity $character The character to be removed.
     *
     * @return void
     */
    public function removeCharacter(CharacterEntity $character): void
    {
        if ($this->characters->contains($character)) {
            $this->characters->removeElement($character);
            if ($character->getUserId() === $this) {
                $character->setUserId(null);
            }
        }
    }

}
