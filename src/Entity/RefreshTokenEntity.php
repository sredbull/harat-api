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

/**
 * Class RefreshTokenEntity.
 *
 * @ORM\Entity
 * @ORM\Table(name="`refresh_token`")
 */
class RefreshTokenEntity implements EntityInterface
{

    /**
     * The id of the refresh token.
     *
     * @var int $id
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * The refresh token.
     *
     * @var string $refreshToken
     *
     * @ORM\Column(type="text", length=16777215)
     */
    private $refreshToken;

    /**
     * The valid state of the token.
     *
     * @var \Datetime $valid
     *
     * @ORM\Column(type="datetime")
     */
    private $valid;

    /**
     * The user the refresh token belongs to.
     *
     * @var UserEntity $user
     *
     * @ORM\OneToOne(targetEntity="App\Entity\UserEntity", inversedBy="refreshToken", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * Get the id of the refresh token.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the id of the refresh token.
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
     * Get the refresh token.
     *
     * @return string
     */
    public function getRefreshToken(): string
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
     * Get the valid state of the refresh token.
     *
     * @return \Datetime
     */
    public function getValid(): \Datetime
    {
        return $this->valid;
    }

    /**
     * Set the valid state of the token.
     *
     * @param \Datetime $valid The state.
     *
     * @return void
     */
    public function setValid(\Datetime $valid): void
    {
        $this->valid = $valid;
    }

    /**
     * Get the user.
     *
     * @return UserEntity
     */
    public function getUser(): UserEntity
    {
        return $this->user;
    }

    /**
     * Set the user.
     *
     * @param UserEntity $user The user.
     *
     * @return void
     */
    public function setUser(UserEntity $user): void
    {
        $this->user = $user;
    }

}
