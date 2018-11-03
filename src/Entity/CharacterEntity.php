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
use JMS\Serializer\Annotation as JMSSerializer;

/**
 * Class CharacterEntity.
 *
 * @ORM\Entity
 * @ORM\Table(name="`character`")
 *
 * @JMSSerializer\ExclusionPolicy("all")
 */
class CharacterEntity implements EntityInterface
{

    /**
     * The id of the character.
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
    private $id;

    /**
     * The id of the character provided by the Eve Sso.
     *
     * @var int $characterId
     *
     * @ORM\Column(type="integer")
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("integer")
     */
    private $characterId;

    /**
     * The name of the character.
     *
     * @var string $characterName
     *
     * @ORM\Column(type="string", length=255)
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     */
    private $characterName;

    /**
     * The security definition scopes.
     *
     * @var array $scopes
     *
     * @ORM\Column(type="array")
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("array")
     */
    private $scopes;

    /**
     * The token type.
     *
     * @var string $tokenType
     *
     * @ORM\Column(type="string", length=255)
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     */
    private $tokenType;

    /**
     * The owner hash of the character.
     *
     * @var string $ownerHash
     *
     * @ORM\Column(type="string", length=255)
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     */
    private $ownerHash;

    /**
     * The refresh token.
     *
     * @var string $refreshToken
     *
     * @ORM\Column(type="string", length=16777216)
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     */
    private $refreshToken;

    /**
     * The access token.
     *
     * @var string $accessToken
     *
     * @ORM\Column(type="string", length=255)
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("string")
     */
    private $accessToken;

    /**
     * The user where the character belongs to.
     *
     * @var UserEntity $user
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\UserEntity", inversedBy="characters")
     *
     * @JMSSerializer\Expose
     * @JMSSerializer\Type("integer")
     * @JMSSerializer\Accessor(getter="getUserId")
     */
    private $user;

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
     * Get the id of the character.
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the character id provided by the Eve Sso.
     *
     * @return integer|null
     */
    public function getCharacterId(): ?int
    {
        return $this->characterId;
    }

    /**
     * Set the character id provided by the Eve Sso.
     *
     * @param integer $characterId The character id provided by the Eve Sso.
     *
     * @return void
     */
    public function setCharacterId(int $characterId): void
    {
        $this->characterId = $characterId;
    }

    /**
     * Get he character name.
     *
     * @return string|null
     */
    public function getCharacterName(): ?string
    {
        return $this->characterName;
    }

    /**
     * Set the character name.
     *
     * @param string $characterName The character name.
     *
     * @return void
     */
    public function setCharacterName(string $characterName): void
    {
        $this->characterName = $characterName;
    }

    /**
     * Get the security definition scopes.
     *
     * @return array|null
     */
    public function getScopes(): ?array
    {
        return $this->scopes;
    }

    /**
     * Set the security definition scopes.
     *
     * @param array $scopes The security definition scopes.
     *
     * @return void
     */
    public function setScopes(array $scopes): void
    {
        $this->scopes = $scopes;
    }

    /**
     * Get the token type.
     *
     * @return string|null
     */
    public function getTokenType(): ?string
    {
        return $this->tokenType;
    }

    /**
     * Set the token type.
     *
     * @param string $tokenType The token type.
     *
     * @return void
     */
    public function setTokenType(string $tokenType): void
    {
        $this->tokenType = $tokenType;
    }

    /**
     * Get the character owner hash.
     *
     * @return string|null
     */
    public function getOwnerHash(): ?string
    {
        return $this->ownerHash;
    }

    /**
     * Set the character owner hash.
     *
     * @param string $ownerHash The character owner hash.
     *
     * @return void
     */
    public function setOwnerHash(string $ownerHash): void
    {
        $this->ownerHash = $ownerHash;
    }

    /**
     * Get the refresh token.
     *
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * Set the refresh token.
     *
     * @param string $refreshToken The refresh token.
     *
     * @return void
     */
    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * Get the access token.
     *
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * Set the access token.
     *
     * @param string $accessToken The access token.
     *
     * @return void
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Get the user where the character belongs to.
     *
     * @return UserEntity|null
     */
    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    /**
     * Get the user id where this recruitment belongs to.
     *
     * @return integer
     */
    public function getUserId(): ?int
    {
        return $this->getUser() ? $this->getUser()->getId() : null;
    }

    /**
     * Set the user where the character belongs to.
     *
     * @param UserEntity $user The user where the character belongs to.
     *
     * @return void
     */
    public function setUser(UserEntity $user): void
    {
        $this->user = $user;
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

}
