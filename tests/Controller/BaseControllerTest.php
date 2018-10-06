<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Tests\Controller;

use App\Controller\BaseController;
use App\Exception\ApiException;
use App\Tests\BaseTestCase;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class BaseControllerTest.
 */
class BaseControllerTest extends BaseTestCase
{

    /**
     * Test if the serializer will be initialized.
     *
     * @throws ApiException         When the controller could not be instantiated.
     * @throws \ReflectionException When Something fails with invoking the method or getting the property value.
     *
     * @return void
     */
    public function testSetSerializer(): void
    {
        $requestStack = new RequestStack();
        $controller = new BaseController($requestStack);
        $this->invokeMethod($controller, 'setSerializer');
        $serializer = $this->getProperty($controller, 'serializer');

        $this->assertInstanceOf(Serializer::class, $serializer);
    }

    /**
     * Test the base controller constructor.
     *
     * @throws \ReflectionException When the constructor could not be reflected.
     *
     * @return void
     */
    public function test__construct(): void
    {
        $requestStack = new RequestStack();

        $mock = $this->getMockBuilder(BaseController::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mock->expects($this->once())
            ->method('setIncludes')
            ->with($requestStack);

        $mock->expects($this->once())
            ->method('setSerializer');

        $this->getConstructor(BaseController::class)->invoke($mock, $requestStack);
    }

    /**
     * Test if the includes could be set with the includes in the request data.
     *
     * @throws ApiException         When the includes are not the type of an array.
     * @throws \ReflectionException When Something fails with invoking the method or getting the property value.
     *
     * @return void
     */
    public function testSetIncludesWithIncludes(): void
    {
        $request = new Request(['includes' => ['test']], [], [], [], [], [], null);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $controller = new BaseController($requestStack);
        $this->invokeMethod($controller, 'setIncludes', [$requestStack]);
        $includes = $this->getProperty($controller, 'includes');

        $this->assertEquals(['test'], $includes);
    }

    /**
     * Test if the includes could be set without the includes in the request data.
     *
     * @throws ApiException         When the includes are not the type of an array.
     * @throws \ReflectionException When Something fails with invoking the method or getting the property value.
     *
     * @return void
     */
    public function testSetIncludesWithoutIncludes(): void
    {
        $requestStack = new RequestStack();

        $controller = new BaseController($requestStack);
        $this->invokeMethod($controller, 'setIncludes', [$requestStack]);
        $includes = $this->getProperty($controller, 'includes');

        $this->assertEquals([], $includes);
    }

    /**
     * Test if the includes could be set with the includes in the request data but not as an array.
     *
     * @throws ApiException         When the includes are not the type of an array.
     * @throws \ReflectionException When Something fails with invoking the method or getting the property value.
     *
     * @return void
     */
    public function testSetIncludesWithInvalidIncludes(): void
    {
        $this->expectException(ApiException::class);

        $request = new Request(['includes' => 'test'], [], [], [], [], [], null);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $controller = new BaseController($requestStack);
        $this->invokeMethod($controller, 'setIncludes', [$requestStack]);
        $includes = $this->getProperty($controller, 'includes');

        $this->assertEquals([], $includes);
    }

    /**
     * Test when the view method is called it properly returns json data.
     *
     * @throws ApiException When the controller could not be instantiated.
     *
     * @return void
     */
    public function testView(): void
    {
        $requestStack = new RequestStack();
        $controller = new BaseController($requestStack);
        $view = $controller->view(['data' => 'test'], 201);
        $this->assertEquals(200, $view->getStatusCode());
        $this->assertEquals('{"test": "data"}', $view->getContent());
    }

}
