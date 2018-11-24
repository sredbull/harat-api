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
 * Class InvalidClassInstanceException.
 *
 * @OA\Response(
 *     response="InvalidClassInstanceException",
 *     description="error operation"
 * )
 */
class InvalidClassInstanceException extends ApiException
{

    /**
     * InvalidClassInstanceException constructor.
     *
     * @param string  $message The Exception message(s).
     * @param integer $code    The exception code.
     */
    public function __construct(
        string $message = 'Invalid class instance',
        int $code = Response::HTTP_METHOD_NOT_ALLOWED
    ) {
        parent::__construct($message, $code);
    }

}
