<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Controller;

use App\Exception\ApiException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Class ExceptionController.
 */
class ExceptionController extends FOSRestController implements ClassResourceInterface
{

    /**
     * Handle exceptions with a custom action.
     *
     * @param \Throwable $exception The exception to handle.
     *
     * @return View
     */
    public function showAction(\Throwable $exception): View
    {
        $originException = $exception;

        if (!$exception instanceof ApiException && !$exception instanceof HttpException) {
            $exception = new HttpException($this->getStatusCode($exception), $this->getStatusText($exception));
        }

        if ($exception instanceof HttpException) {
            $exception = new ApiException($exception->getMessage(), $this->getStatusCode($exception));
        }

        $errorDetails = $exception->getErrorDetails();

        if ($this->isDebugMode() === false) {
            unset($errorDetails['trace']);
        }

        return $this->view(
            $errorDetails,
            $this->getStatusCode($originException)
        );
    }

    /**
     * Checks if the application runs in debug mode.
     *
     * @return boolean
     */
    public function isDebugMode(): bool
    {
        return $this->getParameter('kernel.debug');
    }

    /**
     * Get the status code of the exception.
     *
     * @param \Throwable $exception The exception.
     *
     * @return integer
     */
    protected function getStatusCode(\Throwable $exception): int
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }

        if ($exception->getCode() === 0 && $exception instanceof ApiException) {
            $errorDetails = $exception->getErrorDetails();

            return $errorDetails['code'];
        }

        return 500;
    }

    /**
     * Get the status code of the exception.
     *
     * @param \Throwable $exception The exception.
     * @param string     $default   The default status text.
     *
     * @return string
     */
    protected function getStatusText(\Throwable $exception, string $default = 'Internal Server Error'): string
    {
        $code = $this->getStatusCode($exception);

        return array_key_exists($code, Response::$statusTexts) ? Response::$statusTexts[$code] : $default;
    }

}