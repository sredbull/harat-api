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
use ReflectionClass;
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

        $traceObject = $this->getTrace($exception);

        if (!$exception instanceof ApiException && !$exception instanceof HttpException) {
            $errorDetails = [
                'code' => $this->getStatusCode($exception),
                'status' => 'error',
                'message' => $exception->getMessage() ? $exception->getMessage() : $this->getStatusText($exception),
                'trace' => [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ],
                'trace_details' => json_decode(json_encode($traceObject), true),
            ];

            if ($this->isDebugMode() === false) {
                unset($errorDetails['trace']);
                unset($errorDetails['trace_details']);
            }

            return $this->view(
                $errorDetails
            );
        }

        if ($exception instanceof HttpException) {
            $exception = new ApiException($exception->getMessage(), $this->getStatusCode($exception));
        }

        $errorDetails = $exception->getErrorDetails();

        if ($this->isDebugMode() === true) {
            $errorDetails['trace'] = [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
            $errorDetails['trace_details'] = json_decode(json_encode($traceObject), true);
        }

        return $this->view(
            $errorDetails,
            $this->getStatusCode($originException)
        );
    }

    /**
     * Get the trace from an exception.
     *
     * @param \Throwable $exception The exception to get the trace from.
     *
     * @return \stdClass
     */
    public function getTrace (\Throwable $exception): \stdClass
    {
        $traceObject = new \stdClass();
        foreach ($exception->getTrace() as $index => $trace)
        {
            $traceObject->$index = new \stdClass();
            $traceObject->$index->file = array_key_exists('file', $trace) ? $trace['file'] : null;
            $traceObject->$index->line = array_key_exists('line', $trace) ? $trace['line'] : null;
            $traceObject->$index->function = array_key_exists('function', $trace) ? $trace['function'] : null;
            $traceObject->$index->class = array_key_exists('class', $trace) ? $trace['class'] : null;
            $traceObject->$index->args = new \stdClass();

            foreach ($trace['args'] as $argIndex => $arg) {
                $traceObject->$index->args->$argIndex = new \stdClass();
                $traceObject->$index->args->$argIndex->class = is_object($arg) ? get_class($arg) : null;
                $traceObject->$index->args->$argIndex = $this->getTraceArguments($arg, new \stdClass());
            }
        }

        return $traceObject;
    }

    /**
     * Get the trace arguments.
     *
     * @param string|array $arguments The arguments.
     * @param \stdClass    $object    The trace object.
     *
     * @return mixed
     */
    public function getTraceArguments ($arguments, \stdClass $object)
    {
        if (is_string($arguments) === true) {
            return $arguments;
        }

        if (is_array($arguments) === true) {
            return $arguments;
        }

        if (is_object($arguments) === true) {
            try {
                $argumentsReflectionClass = new ReflectionClass($arguments);
                foreach ($argumentsReflectionClass->getProperties() as $property) {
                    $property->setAccessible(true);
                    $values = $property->getValue($arguments);
                    $name = $property->getName();
                    $object->$name = new \stdClass();
                    $object->$name = $this->getTraceArgumentsValues($values, $object->$name);

                }
            } catch (\ReflectionException $exception) {
                // Just catch the exception.
            }
        }

        return $object;
    }

    /**
     * Get the trace argument values.
     *
     * @param string|array $values The values.
     * @param \stdClass    $object The original object.
     *
     * @return mixed
     */
    public function getTraceArgumentsValues ($values, \stdClass $object)
    {
        if (is_string($values) === true || is_array($values) === false) {
            return $values;
        }
        if (is_array($values) === true) {
            $index = 0;
            foreach ($values as $value) {
                if (is_object($value) === false) {
                    return $values;
                }

                $object->$index = new \stdClass();
                $object->$index->class = get_class($value);
                $object->$index = $this->getTraceArguments($value, $object->$index);
                $index = $index + 1;
            }
        }

        return $object;
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