<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\ParamConverter\EveSso;

use App\Interfaces\RequestObjectInterface;
use Fesor\RequestObject\RequestObject;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PostLoginRequest.
 */
class GetCallbackRequest extends RequestObject implements RequestObjectInterface
{

    /**
     * The redirect url.
     *
     * @var string $code
     */
    private $code;

    /**
     * The redirect url.
     *
     * @var string $redirect
     */
    private $redirect;

    /**
     * The state.
     *
     * @var string $state
     */
    private $state;

    /**
     * The user id.
     *
     * @var string $userId
     */
    private $userId;

    /**
     * Set the rules for this request.
     *
     * @return Assert\Collection
     */
    public function rules(): Assert\Collection
    {
        return new Assert\Collection([
            'code' => [
                new Assert\Type(['type' => 'string']),
            ],
            'redirect' => [
                new Assert\Type(['type' => 'string']),
            ],
            'state' => [
                new Assert\Type(['type' => 'string']),
            ],
            'userId' => [
                new Assert\Type(['type' => 'string']),
            ],
        ]);
    }

    /**
     * Get the code.
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Get the redirect.
     *
     * @return string
     */
    public function getRedirect(): string
    {
        return $this->redirect;
    }

    /**
     * Get the user id.
     *
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * Get the state.
     *
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

}
