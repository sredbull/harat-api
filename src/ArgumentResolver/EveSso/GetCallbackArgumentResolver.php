<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\ArgumentResolver\EveSso;

use App\ArgumentResolver\BaseArgumentResolver;
use App\Exception\InvalidContentException;
use App\Exception\InvalidContentTypeException;
use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GetCallbackArgumentResolver.
 */
class GetCallbackArgumentResolver extends BaseArgumentResolver
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
     * Checks if the class supports to resolve its arguments.
     *
     * @param Request          $request  The request.
     * @param ArgumentMetadata $argument The argument meta data.
     *
     * @return boolean
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === GetCallbackArgumentResolver::class;
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

        $this->code = $content['code'];
        $this->redirect = $content['redirect'];
        $this->state = $content['state'];
        $this->userId = $content['userId'];

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
