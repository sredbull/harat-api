<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\ArgumentResolver\Login;

use App\ArgumentResolver\BaseArgumentResolver;
use App\Exception\InvalidContentException;
use App\Exception\InvalidContentTypeException;
use App\Exception\ValidationException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\Schema(schema="PostLoginArgumentResolver")
 */
class PostLoginArgumentResolver extends BaseArgumentResolver
{

    /**
     * The username field of the login request.
     *
     * @var string $username
     *
     * @OA\Property()
     */
    private $username;

    /**
     * The password field of the login request.
     *
     * @var string $password
     *
     * @OA\Property()
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
        return $argument->getType() === PostLoginArgumentResolver::class;
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
            'username' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'string']),
            ],
            'password' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'string']),
            ],
        ]);
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
        return $this->password;
    }

}
