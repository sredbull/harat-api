<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Exception;

use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AuthenticationFailedException.
 *
 * @OA\Response(
 *     response="AuthenticationFailedException",
 *     description="error operation"
 * )
 */
class AuthenticationFailedException extends ApiException
{

    /**
     * AuthenticationFailedException constructor.
     *
     * @param string  $message The Exception message(s).
     * @param integer $code    The exception code.
     */
    public function __construct(
        string $message = 'Authentication failed.',
        int $code = Response::HTTP_FORBIDDEN
    ) {
        parent::__construct($message, $code);
    }

}
