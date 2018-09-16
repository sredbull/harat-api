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

use App\Interfaces\ApiExceptionInterface;

/**
 * Class ApiException.
 */
class ApiException extends \Exception implements ApiExceptionInterface
{

    /**
     * ApiException constructor.
     *
     * @param string  $message The exception message.
     * @param integer $code    The HTTP error code.
     */
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }

}
