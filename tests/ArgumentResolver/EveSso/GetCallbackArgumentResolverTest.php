<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Tests\ArgumentResolver\EveSso;

use App\ArgumentResolver\EveSso\GetCallbackArgumentResolver;
use App\Exception\InvalidContentException;
use App\Exception\InvalidContentTypeException;
use App\Exception\ValidationException;
use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class GetCallbackArgumentResolverTest.
 */
class GetCallbackArgumentResolverTest extends BaseTestCase
{

    /**
     * Test if supports returns true when the class is supported.
     *
     * @return void
     */
    public function testSupportsIsTrue(): void
    {
        $request = new Request([], [], [], [], [], [], null);
        $validator = $this->createMock(ValidatorInterface::class);
        $argumentMetadata = new ArgumentMetadata('resolver', GetCallbackArgumentResolver::class, false, false, null);
        $argumentResolver = new GetCallbackArgumentResolver($validator);

        $this->assertTrue($argumentResolver->supports($request, $argumentMetadata));
    }

    /**
     * Test if supports returns false when the class is not supported.
     *
     * @return void
     */
    public function testSupportsIsFalse(): void
    {
        $request = new Request([], [], [], [], [], [], null);
        $validator = $this->createMock(ValidatorInterface::class);
        $argumentMetadata = new ArgumentMetadata('resolver', 'App\Class\NotSupported', false, false, null);
        $argumentResolver = new GetCallbackArgumentResolver($validator);

        $this->assertFalse($argumentResolver->supports($request, $argumentMetadata));
    }

    /**
     * Test if it resolves a valid request.
     *
     * @throws InvalidContentException     When the content type is not in json.
     * @throws ValidationException         When the validation fails.
     * @throws InvalidContentTypeException When the content type is not "application/json".
     *
     * @return void
     */
    public function testResolveValidRequest(): void
    {
        $data = [
            'code' => 'code',
            'redirect' => 'redirect',
            'state' => 'state',
            'userId' => 'userId',

        ];

        $rules = new Assert\Collection([
            'code' => [
                new Assert\Type(['type' => 'string']),
            ],
            'redirect' => [
                new Assert\Type(['type' => 'string']),
            ],
            'state' => [
                new Assert\Type(['type' => 'string']),
            ],
            'userId' => [
                new Assert\Type(['type' => 'string']),
            ],
        ]);

        $group = new Assert\GroupSequence(['Default']);

        $request = new Request([], [], [], [], [], ['CONTENT_TYPE' => ['application/json']], json_encode($data));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->with($data, $rules, $group)
            ->willReturn([]);
        $argumentMetadata = new ArgumentMetadata('resolver', GetCallbackArgumentResolver::class, false, false, null);
        $argumentResolver = new GetCallbackArgumentResolver($validator

        );
        $request = $argumentResolver->resolve($request, $argumentMetadata);

        $this->assertEquals('code', $request->current()->getCode());
        $this->assertEquals('redirect', $request->current()->getRedirect());
        $this->assertEquals('state', $request->current()->getState());
        $this->assertEquals('userId', $request->current()->getUserId());

        $request->next();
    }

    /**
     * Tests if it throws an InvalidContentException when no content is sent with the request.
     *
     * @throws InvalidContentException     When the content type is not in json.
     * @throws ValidationException         When the validation fails.
     * @throws InvalidContentTypeException When the content type is not "application/json".
     *
     * @return void
     */
    public function testResolveInvalidRequestContent(): void
    {
        $this->expectException(InvalidContentException::class);
        $request = new Request([], [], [], [], [], ['CONTENT_TYPE' => ['application/json']], null);

        $validator = $this->createMock(ValidatorInterface::class);
        $argumentMetadata = new ArgumentMetadata('resolver', GetCallbackArgumentResolver::class, false, false, null);
        $argumentResolver = new GetCallbackArgumentResolver($validator);
        $request = $argumentResolver->resolve($request, $argumentMetadata);
        $request->current();
    }

    /**
     * Tests if it throws an ValidationException when the content is not valid.
     *
     * @throws InvalidContentException     When the content type is not in json.
     * @throws ValidationException         When the validation fails.
     * @throws InvalidContentTypeException When the content type is not "application/json".
     *
     * @return void
     */
    public function testResolveInvalidRequestBody(): void
    {
        $this->expectException(ValidationException::class);
        $request = new Request([], [], [], [], [], ['CONTENT_TYPE' => ['application/json']], '{}');

        $validator = $this->createMock(ValidatorInterface::class);
        $violations = new ConstraintViolationList(array(
            $this->createMock(ConstraintViolation::class),
        ));
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn($violations);
        $argumentMetadata = new ArgumentMetadata('resolver', GetCallbackArgumentResolver::class, false, false, null);
        $argumentResolver = new GetCallbackArgumentResolver($validator);
        $request = $argumentResolver->resolve($request, $argumentMetadata);
        $request->current();
    }

}
