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
class ValidationException extends ApiException
{

    /**
     * The validation errors.
     *
     * @var ConstraintViolationListInterface $validationErrors
     */
    private $validationErrors;

    /**
     * ValidationException constructor.
     *
     * @param ConstraintViolationListInterface $validationErrors The validation errors.
     */
    public function __construct(ConstraintViolationListInterface $validationErrors)
    {
        $this->validationErrors = $validationErrors;
    }

    /**
     * Get the exception details.
     *
     * @return array
     */
    public function getErrorDetails(): array
    {
        return [
            'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'status' => 'error',
            'message' => Response::$statusTexts[422],
            'errors' => $this->validationErrors,
        ];
    }

}