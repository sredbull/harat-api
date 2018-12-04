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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation as JMSSerializer;
use OpenApi\Annotations as OA;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

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
 *
 * @OA\Schema(schema="UserEntity")
 */
class UserEntity extends BaseUser implements EntityInterface
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
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     */
    protected $username;

    /**
     * The email of the user.
     *
     * @var string $email
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
     * @JMSSerializer\Expose(if="isIncluded('groups')")
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
     * @var array $character
     *
     * @ORM\OneToMany(targetEntity="App\Entity\CharacterEntity", mappedBy="user", cascade={"persist"})
     *
     * @JMSSerializer\Expose(if="isIncluded('characters')")
     * @JMSSerializer\Type("array")
     */
    private $characters;

    /**
     * The recruitments of this user.
     *
     * @var array $recruitments
     *
     * @ORM\OneToMany(targetEntity="App\Entity\RecruitmentEntity", mappedBy="user")
     *
     * @JMSSerializer\Expose(if="isIncluded('recruitments')")
     * @JMSSerializer\Type("array")
     */
    private $recruitments;

    /**
     * The refresh token.
     *
     * @var RefreshTokenEntity $refreshToken
     *
     * @ORM\OneToOne(targetEntity="App\Entity\RefreshTokenEntity", mappedBy="user", cascade={"persist", "remove"})
     */
    private $refreshToken;

    /**
     * UserEntity constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->characters = new ArrayCollection();
        $this->recruitments = new ArrayCollection();
    }

    /**
     * Get the avatar.
     *
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * Set the avatar.
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
            $character->setUser($this);
        }
    }

    /**
     * Remove a character from this user.
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
     * Get the recruitment's for this user.
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
     * Remove a recruitment from this user.
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
     * Get the refresh token.
     *
     * @return RefreshTokenEntity|null
     */
    public function getRefreshToken(): ?RefreshTokenEntity
    {
        return $this->refreshToken;
    }

    /**
     * Set the refresh token.
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

}
