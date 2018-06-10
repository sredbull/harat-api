<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\ParamConverter\Registration;

use JMS\Serializer\Annotation as JMSSerializer;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AppAssert;

/**
 * Class RegisterUserRequest.
 */
class PostRegistrationParamConverter
{

    /**
     * The email field of the registration.
     *
     * @var string $email
     *
     * @Assert\NotBlank
     * @Assert\Email(
     *     strict = true,
     *     checkMX = true,
     *     checkHost = true
     * )
     *
     * @AppAssert\ExistingLdapUser()
     *
     * @JMSSerializer\Type("string")
     */
    private $email;

    /**
     * The username field of the registration request.
     *
     * @var string $username
     *
     * @Assert\NotBlank
     *
     * @AppAssert\ExistingLdapUser()
     *
     * @JMSSerializer\Type("string")
     */
    private $username;

    /**
     * The password field of the registration request.
     *
     * @var array $password
     *
     * @Assert\NotBlank
     * @Assert\Collection(
     *     fields = {
     *         "first" = {@Assert\NotBlank, @Assert\IdenticalTo(propertyPath="password[second]")},
     *         "second" = {@Assert\NotBlank, @Assert\IdenticalTo(propertyPath="password[first]")}
     *     }
     * )
     *
     * @JMSSerializer\Type("array")
     */
    private $password;

    /**
     * Get the email.
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the email.
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
