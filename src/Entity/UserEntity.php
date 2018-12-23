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

use App\Annotation\HrefLink;
use App\Annotation\Linked;
use App\Interfaces\EntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserEntity.
 *
 * @ORM\Entity
 * @ORM\Table(name="user")
 *
 * @HrefLink(href="/user")
 *
 * @OA\Schema(schema="UserEntity")
 */
class UserEntity implements EntityInterface, UserInterface
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
     * @OA\Property()
     */
    private $id;

    /**
     * The name of the user.
     *
     * @var string $username
     *
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Groups({"user"})
     *
     * @OA\Property()
     */
    private $username;

    /**
     * The email of the user.
     *
     * @var string $email
     *
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @OA\Property()
     */
    private $email;

    /**
     * The enabled state of the user.
     *
     * @var bool $enabled
     *
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * The password of the user.
     *
     * @var string $password
     *
     * @ORM\Column(type="string", length=255)
     *
     * @JMS\Exclude()
     */
    private $password;

    /**
     * The salt of the user.
     *
     * @var string|null $salt
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @JMS\Exclude()
     */
    private $salt;

    /**
     * The last login date of the user.
     *
     * @var \DateTime|null $lastLogin
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * The avatar url of the user.
     *
     * @var string $avatar
     *
     * @ORM\Column(type="string", length=2048, nullable=true)
     *
     * @OA\Property()
     */
    private $avatar;

    /**
     * The roles of the user.
     *
     * @var array $roles
     *
     * @ORM\Column(type="json")
     *
     * @JMS\Exclude()
     */
    private $roles;

    /**
     * The groups of the user.
     *
     * @var GroupEntity[]|Collection $groups
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\GroupEntity")
     * @ORM\JoinTable(name="user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     *
     * @OA\Property(type="array", @OA\Items(ref="#/components/schemas/GroupEntity"))
     */
    private $groups;

    /**
     * The characters of this user.
     *
     * @var ArrayCollection $character
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CharacterEntity", mappedBy="user", cascade={"persist"})
     *
     * @OA\Property(type="array", @OA\Items(ref="#/components/schemas/CharacterEntity"))
     *
     * @JMS\Exclude()
     *
     * @Linked(href="/character", accessor="getCharacters")
     */
    private $characters;

    /**
     * The recruitments of this user.
     *
     * @var ArrayCollection $recruitments
     *
     * @ORM\OneToMany(targetEntity="App\Entity\RecruitmentEntity", mappedBy="user")
     *
     * @OA\Property(type="array", @OA\Items(ref="#/components/schemas/RecruitmentEntity"))
     *
     * @JMS\Exclude()
     *
     * @Linked(href="/recruitment", accessor="getRecruitments")
     */
    private $recruitments;

    /**
     * The refresh token of this user.
     *
     * @var RefreshTokenEntity $refreshToken
     *
     * @ORM\OneToOne(targetEntity="App\Entity\RefreshTokenEntity", mappedBy="user", cascade={"persist", "remove"})
     *
     * @OA\Property(ref="#/components/schemas/RefreshTokenEntity")
     *
     * @JMS\Accessor(getter="getTrimmedRefreshToken")
     */
    private $refreshToken;

    /**
     * UserEntity constructor.
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->characters = new ArrayCollection();
        $this->recruitments = new ArrayCollection();
    }

    /**
     * Get the id of the user.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the id of the user.
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
     * Get the username of this user.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Set the username of this user.
     *
     * @param string $username The username.
     *
     * @return void
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * Get the email of this user.
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the email of this user.
     *
     * @param string $email The email.
     *
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Check the enabled state of this user.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Set the enabled state of this user.
     *
     * @param bool $enabled The enabled state.
     *
     * @return void
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Get the password of this user.
     *
     * @return string|null
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the password of this user.
     *
     * @param string $password The password.
     *
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * Get the salt of this user.
     *
     * @return string
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * Set the salt of this user.
     *
     * @param string|null $salt The salt.
     *
     * @return void
     */
    public function setSalt(?string $salt): void
    {
        $this->salt = $salt;
    }

    /**
     * Get the last login date of this user.
     *
     * @return \DateTime|null
     */
    public function getLastLogin(): ?\DateTime
    {
        return $this->lastLogin;
    }

    /**
     * Set the last login date of this user.
     *
     * @param \DateTime|null $lastLogin The last logged in date.
     *
     * @return void
     */
    public function setLastLogin(?\DateTime $lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * Get the avatar of this user.
     *
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * Set the avatar of this user.
     *
     * @param string|null $avatar The url of the avatar.
     *
     * @return void
     */
    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * Get the roles of this user.
     *
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Set the roles of this user.
     *
     * @param array $roles The roles.
     *
     * @return void
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * Get the groups of this user.
     *
     * @return GroupEntity[]|Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add a group to this user.
     *
     * @param GroupEntity $group The group to be added.
     *
     * @return void
     */
    public function addGroup(GroupEntity $group): void
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }
    }

    /**
     * Remove a group of this user.
     *
     * @param GroupEntity $group The group.
     *
     * @return void
     */
    public function removeGroup(GroupEntity $group): void
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
        }
    }

    /**
     * Get the characters of this user.
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
            $character->setUser($this);
        }
    }

    /**
     * Remove a character of this user.
     *
     * @param CharacterEntity $character The character to be removed.
     *
     * @return void
     */
    public function removeCharacter(CharacterEntity $character): void
    {
        if ($this->characters->contains($character)) {
            $this->characters->removeElement($character);
            if ($character->getUser() === $this) {
                $character->setUser(null);
            }
        }
    }

    /**
     * Get the recruitment's of this user.
     *
     * @return Collection|RecruitmentEntity[]
     */
    public function getRecruitments(): Collection
    {
        return $this->recruitments;
    }

    /**
     * Add a recruitment to this user.
     *
     * @param RecruitmentEntity $recruitment The recruitment.
     *
     * @return void
     */
    public function addRecruitment(RecruitmentEntity $recruitment): void
    {
        if (!$this->recruitments->contains($recruitment)) {
            $this->recruitments[] = $recruitment;
            $recruitment->setUser($this);
        }
    }

    /**
     * Remove a recruitment of this user.
     *
     * @param RecruitmentEntity $recruitment The recruitment.
     *
     * @return void
     */
    public function removeRecruitment(RecruitmentEntity $recruitment): void
    {
        if ($this->recruitments->contains($recruitment)) {
            $this->recruitments->removeElement($recruitment);
        }
    }

    /**
     * Get the refresh token of this user.
     *
     * @return RefreshTokenEntity|null
     */
    public function getRefreshToken(): ?RefreshTokenEntity
    {
        return $this->refreshToken;
    }

    /**
     * Get the actual refresh token of this user.
     *
     * @return string|null
     */
    public function getTrimmedRefreshToken(): ?string
    {
        return $this->getRefreshToken() ? $this->getRefreshToken()->getRefreshToken() : null;
    }

    /**
     * Set the refresh token of this user.
     *
     * @param RefreshTokenEntity $refreshToken The refresh token.
     *
     * @return void
     */
    public function setRefreshToken(RefreshTokenEntity $refreshToken): void
    {
        $this->refreshToken = $refreshToken;

        if ($this !== $refreshToken->getUser()) {
            $refreshToken->setUser($this);
        }
    }

    /**
     * Erase the credentials of this user.
     *
     * @return void
     */
    public function eraseCredentials(): void
    {
        $this->salt = null;
    }

}
