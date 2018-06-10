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

/**
 * Class ApiException.
 */
class ApiException extends \Exception
{

    /**
     * Get the exception details.
     *
     * @return array
     */
    public function getErrorDetails(): array
    {
        return [
            'code' => $this->getCode() ? $this->getCode() : 500,
            'status' => 'error',
            'message' => $this->getMessage() ? $this->getMessage() : 'API Exception',
        ];
    }

}
