<?php /** @noinspection PhpCSValidationInspection */
declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\ParamConverter\Registration;

use App\Interfaces\RequestObjectInterface;
use App\Validator\Constraints as AppAssert;
use Fesor\RequestObject\RequestObject;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PostLoginRequest.
 */
class PostRegisterRequest extends RequestObject implements RequestObjectInterface
{

    /**
     * The email field of the registration.
     *
     * @var string $email
     */
    private $email;

    /**
     * The username field of the login request.
     *
     * @var string $username
     */
    private $username;

    /**
     * The password field of the login request.
     *
     * @var array $password
     */
    private $password;

    /**
     * Set the rules for this request.
     *
     * @return Assert\Collection
     */
    public function rules(): Assert\Collection
    {
        return new Assert\Collection([
            'email' => [
                new Assert\NotBlank(['groups' => 'first']),
                new Assert\Type(['type' => 'string', 'groups' => 'first']),
                new Assert\Email(['checkMX' => true, 'checkHost' => true, 'groups' => 'second', 'mode' => 'strict']),
                new AppAssert\ExistingLdapUser(['type' => 'email', 'groups' => 'third']),
            ],
            'username' => [
                new Assert\NotBlank(['groups' => 'first']),
                new Assert\Type(['type' => 'string', 'groups' => 'second']),
                new Assert\Length(['min' => 3, 'groups' => 'second']),
                new AppAssert\ExistingLdapUser(['type' => 'username', 'groups' => 'third']),
            ],
            'password' => [
                new Assert\NotBlank(['groups' => 'first']),
                new Assert\Type(['type' => 'array', 'groups' => 'second']),
                new Assert\Collection([
                    'first' => [
                        new Assert\NotBlank(['groups' => 'third']),
                        new Assert\Type(['type' => 'string', 'groups' => 'third']),
                        new AppAssert\MatchingPassword(['groups' => 'fourth', 'propertyPath' => '[password][second]']),
                    ],
                    'second' => [
                        new Assert\NotBlank(['groups' => 'third']),
                        new Assert\Type(['type' => 'string', 'groups' => 'third']),
                    ],
                ]),
            ],
        ]);
    }

    /**
     * Set the validation sequence.
     *
     * @param array|null $payload The payload.
     *
     * @return Assert\GroupSequence
     */
    public function validationGroup(array $payload): Assert\GroupSequence
    {
        return new Assert\GroupSequence(['first', 'second', 'third', 'fourth']);
    }

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
     * Get the username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Get the password.
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password[0];
    }

}
