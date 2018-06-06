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
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class ValidationException.
 */
class UserNotFoundException extends ApiException
{

    /**
     * Get the exception details.
     *
     * @return array
     */
    public function getErrorDetails(): array
    {
        return [
            'code' => Response::HTTP_NOT_FOUND,
            'status' => 'error',
            'message' => 'User not found',
            'trace' => $this->getTraceAsString(),
        ];
    }

}
