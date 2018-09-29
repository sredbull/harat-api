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
 * Class LdapUserNotFoundException.
 */
class LdapUserNotFoundException extends ApiException
{

    /**
     * LdapUserNotFoundException constructor.
     *
     * @param string  $message The Exception message(s).
     * @param integer $code    The exception code.
     */
    public function __construct(
        string $message = 'User not found',
        int $code = Response::HTTP_NOT_FOUND
    ) {
        parent::__construct($message, $code);
    }

}
