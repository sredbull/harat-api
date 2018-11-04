<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\EventListener;

use App\Exception\ValidationException;
use ReflectionClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Class ApiExceptionListener
 */
class ApiExceptionListener
{

    /**
     * Fired on
     *
     * @param GetResponseForExceptionEvent $event The event with the exception.
     *
     * @return void
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $errors = [];
        if ($event->getException() instanceof ValidationException) {
            /** @var ValidationException $exception */
            $exception = $event->getException();
            foreach($exception->getValidationErrors() as $error)
            {
                $errors[$error->getPropertyPath()] = $error->getMessage();
            }
        }

        $traceObject = $this->getTrace($event->getException());
        $errorDetails = [
            'message' => $event->getException()->getMessage(),
            'errors' => $errors,
            'trace' => [
                'file' => $event->getException()->getFile(),
                'line' => $event->getException()->getLine(),
            ],
            'trace_details' => json_decode(json_encode($traceObject), true),
        ];

        if (count($errors) === 0) {
            unset($errorDetails['errors']);
        }

        if (getenv('APP_ENV') !== 'dev') {
            unset($errorDetails['trace'], $errorDetails['trace_details']);
        }

        $response = new JsonResponse(json_encode($errorDetails), $this->getErrorCode($event->getException()), [], true);
        $event->setResponse($response);
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
            $traceObject->$index->file = $trace['file'] ?? null;
            $traceObject->$index->line = $trace['line'] ?? null;
            $traceObject->$index->function = $trace['function'] ?? null;
            $traceObject->$index->class = $trace['class'] ?? null;
            $traceObject->$index->args = new \stdClass();

            foreach ($trace['args'] as $argIndex => $arg) {
                $traceObject->$index->args->$argIndex = new \stdClass();
                $traceObject->$index->args->$argIndex->class = \is_object($arg) ? \get_class($arg) : null;
                $traceObject->$index->args->$argIndex = $this->getTraceArguments($arg, new \stdClass());
            }
        }

        return $traceObject;
    }

    /**
     * Get the trace arguments.
     *
     * @param string|array|object $arguments The arguments.
     * @param \stdClass           $object    The trace object.
     *
     * @return mixed
     */
    public function getTraceArguments ($arguments, \stdClass $object)
    {
        if (\is_string($arguments) === true) {
            return $arguments;
        }

        if (\is_array($arguments) === true) {
            return $arguments;
        }

        if (\is_object($arguments) === true) {
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
        if (\is_string($values) === true || \is_array($values) === false) {
            return $values;
        }

        if (\is_array($values) === true) {
            $index = 0;
            foreach ($values as $value) {
                if (\is_object($value) === false) {
                    return $values;
                }

                $object->$index = new \stdClass();
                $object->$index->class = \get_class($value);
                $object->$index = $this->getTraceArguments($value, $object->$index);
                ++$index;
            }
        }

        return $object;
    }

    /**
     * Get the error code for this exception.
     *
     * @param \Throwable $exception The exception.
     *
     * @return integer
     */
    public function getErrorCode (\Throwable $exception): int
    {
        if ($exception instanceof ValidationException) {
            return Response::HTTP_UNPROCESSABLE_ENTITY;
        }

        return $exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR;
    }

}
