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

use Symfony\Component\HttpFoundation\Response;

/**
 * Class ValidationException.
 */
class RegistrationFailedException extends ApiException
{

    /**
     * RegistrationFailedException constructor.
     *
     * @param string  $message The Exception message(s).
     * @param integer $code    The exception code.
     */
    public function __construct(
        string $message = 'Registration failed',
        int $code = Response::HTTP_BAD_REQUEST
    ) {
        parent::__construct($message, $code);
    }

}
