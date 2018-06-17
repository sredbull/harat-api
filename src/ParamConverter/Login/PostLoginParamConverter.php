<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\ParamConverter\Login;

use JMS\Serializer\Annotation as JMSSerializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RegisterUserRequest.
 */
class PostLoginParamConverter
{

    /**
     * The username field of the login request.
     *
     * @var string $username
     *
     * @Assert\NotBlank
     * @Assert\GreaterThan(
     *     value = 3
     * )
     *
     * @JMSSerializer\Type("string")
     */
    private $username;

    /**
     * The password field of the login request.
     *
     * @var array $password
     *
     * @Assert\NotBlank
     *
     * @JMSSerializer\Type("string")
     */
    private $password;

    /**
     * Get the username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Set the username.
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
     * Get the password.
     *
     * @return array
     */
    public function getPassword(): array
    {
        return $this->password;
    }

    /**
     * Set the password.
     *
     * @param array $password The password.
     *
     * @return void
     */
    public function setPassword(array $password): void
    {
        $this->password = $password;
    }

}
