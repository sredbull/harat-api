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

/**
 * Class CharacterEntity.
 *
 * @ORM\Entity
 * @ORM\Table(name="`character`")
 */
class CharacterEntity
{

    /**
     * The id of the character.
     *
     * @var int $id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The id of the character provided by the Eve Sso.
     *
     * @var int $characterId
     *
     * @ORM\Column(type="integer")
     */
    private $characterId;

    /**
     * The name of the character.
     *
     * @var string $characterName
     *
     * @ORM\Column(type="string", length=255)
     */
    private $characterName;

    /**
     * The security definition scopes.
     *
     * @var array $scopes
     *
     * @ORM\Column(type="array")
     */
    private $scopes;

    /**
     * The token type.
     *
     * @var string $tokenType
     *
     * @ORM\Column(type="string", length=255)
     */
    private $tokenType;

    /**
     * The owner hash of the character.
     *
     * @var string $ownerHash
     *
     * @ORM\Column(type="string", length=255)
     */
    private $ownerHash;

    /**
     * The refresh token.
     *
     * @var string $refreshToken
     *
     * @ORM\Column(type="string", length=255)
     */
    private $refreshToken;

    /**
     * The access token.
     *
     * @var string $accessToken
     *
     * @ORM\Column(type="string", length=255)
     */
    private $accessToken;

    /**
     * The user where the character belongs to.
     *
     * @var UserEntity $user
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\UserEntity", inversedBy="characters")
     */
    private $user;

    /**
     * The avatar url of the member.
     *
     * @var string $avatar
     *
     * @ORM\Column(type="string", length=2048, nullable=true)
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
     * @return null|string
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
     * @return null|string
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
     * @return null|string
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
     * @return null|string
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
     * @return null|string
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
    public function getUserId(): ?UserEntity
    {
        return $this->user;
    }

    /**
     * Set the user where the character belongs to.
     *
     * @param UserEntity|null $user The user where the character belongs to.
     *
     * @return void
     */
    public function setUserId(?UserEntity $user): void
    {
        $this->user = $user;
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

}

