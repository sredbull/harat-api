<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Class BaseTestCase.
 */
class BaseTestCase extends TestCase
{

    /**
     * Get the constructor of a class.
     *
     * @param string $class The classname.
     *
     * @return \ReflectionMethod
     *
     * @throws \ReflectionException When the class could not be reflected.
     */
    public function getConstructor(string $class): \ReflectionMethod
    {
        $reflectedClass = new \ReflectionClass($class);

        return $reflectedClass->getConstructor();
    }

    /**
     * Invoke a method for the given class.
     *
     * @param mixed  $class      The class.
     * @param string $methodName The method.
     * @param array  $parameters The parameters.
     *
     * @return mixed
     *
     * @throws \ReflectionException When the class could not be reflected.
     */
    public function invokeMethod($class, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(\get_class($class));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($class, $parameters);
    }

    /**
     * Get the value of a property for the given class.
     *
     * @param mixed  $class    The class.
     * @param string $property The property.
     *
     * @return mixed
     *
     * @throws \ReflectionException When the class could not be reflected.
     */
    public function getProperty($class, string $property)
    {
        $reflection = new \ReflectionClass(\get_class($class));
        $value = $reflection->getProperty($property);
        $value->setAccessible(true);

        return $value->getValue($class);
    }

}
