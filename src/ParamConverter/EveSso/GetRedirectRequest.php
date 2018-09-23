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
class GetRedirectRequest extends RequestObject implements RequestObjectInterface
{

    /**
     * The redirect url.
     *
     * @var string $redirect
     */
    private $redirect;

    /**
     * Set the rules for this request.
     *
     * @return Assert\Collection
     */
    public function rules(): Assert\Collection
    {
        return new Assert\Collection([
            'fields' => [
                'redirect' => [
                    new Assert\Type(['type' => 'string']),
                ],
            ],
        ]);
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

}
