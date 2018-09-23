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

use App\Interfaces\RequestObjectInterface;
use Fesor\RequestObject\RequestObject;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PostLoginRequest.
 */
class PostLoginRequest extends RequestObject implements RequestObjectInterface
{

    /**
     * The username field of the login request.
     *
     * @var string $username
     */
    private $username;

    /**
     * The password field of the login request.
     *
     * @var string $password
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
