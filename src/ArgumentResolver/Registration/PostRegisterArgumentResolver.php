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
namespace App\ArgumentResolver\Registration;

use App\ArgumentResolver\BaseArgumentResolver;
use App\Exception\InvalidContentException;
use App\Exception\InvalidContentTypeException;
use App\Exception\ValidationException;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PostLoginRequest.
 */
class PostRegisterArgumentResolver extends BaseArgumentResolver
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
     * Checks if the class supports to resolve its arguments.
     *
     * @param Request          $request  The request.
     * @param ArgumentMetadata $argument The argument meta data.
     *
     * @return boolean
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === PostRegisterArgumentResolver::class;
    }

    /**
     * Resolve the request.
     *
     * @param Request          $request  The request.
     * @param ArgumentMetadata $argument The argument meta data.
     *
     * @return \Generator
     *
     * @throws InvalidContentException     When invalid content is detected.
     * @throws InvalidContentTypeException When the content type is invlaid.
     * @throws ValidationException         When validation fails.
     */
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $content = $this->getRequestContent($request);
        $this->validate($content);

        $this->email = $content['email'];
        $this->username = $content['username'];
        $this->password = $content['password'];

        yield $this;
    }

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
     * @return Assert\GroupSequence
     */
    public function validationGroup(): Assert\GroupSequence
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
