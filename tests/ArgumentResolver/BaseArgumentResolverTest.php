<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Tests\ArgumentResolver\Login;

use App\ArgumentResolver\BaseArgumentResolver;
use App\Exception\InvalidContentException;
use App\Exception\InvalidContentTypeException;
use App\Exception\ValidationException;
use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\TraceableValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class BaseArgumentResolverTest.
 */
class BaseArgumentResolverTest extends BaseTestCase
{

    /**
     * Test if supports returns true when the class is supported.
     *
     * @return void
     */
    public function testSupports(): void
    {
        $request = new Request([], [], [], [], [], [], null);
        $validator = $this->createMock(TraceableValidator::class);
        $argumentMetadata = new ArgumentMetadata('resolver', BaseArgumentResolver::class, false, false, null);
        $argumentResolver = new BaseArgumentResolver($validator);

        $this->assertFalse($argumentResolver->supports($request, $argumentMetadata));
    }

    /**
     * Test if it resolves a valid request.
     *
     * @return void
     */
    public function testResolve(): void
    {
        $request = new Request([], [], [], [], [], [], null);
        $validator = $this->createMock(TraceableValidator::class);
        $argumentMetadata = new ArgumentMetadata('resolver', BaseArgumentResolver::class, false, false, null);
        $argumentResolver = new BaseArgumentResolver($validator);
        $request = $argumentResolver->resolve($request, $argumentMetadata);

        $this->assertInstanceOf(BaseArgumentResolver::class, $request->current());
        $request->next();
    }

    /**
     * Test if the rules are returned.
     *
     * @return void
     */
    public function testRules(): void
    {
        $validator = $this->createMock(TraceableValidator::class);
        $argumentResolver = new BaseArgumentResolver($validator);

        $this->assertEquals(new Assert\Collection([]), $argumentResolver->rules());
    }

    /**
     * Tests if it throws an InvalidContentTypeException when wrong content type is sent with the request.
     *
     * @throws InvalidContentException     When the content type is not in json.
     * @throws InvalidContentTypeException When the content type is not "application/json".
     *
     * @return void
     */
    public function testGetRequestInvalidContentType(): void
    {
        $this->expectException(InvalidContentTypeException::class);
        $request = new Request([], [], [], [], [], ['CONTENT_TYPE' => ['application/xml']], null);

        $validator = $this->createMock(ValidatorInterface::class);
        $argumentResolver = new BaseArgumentResolver($validator);
        $argumentResolver->getRequestContent($request);
    }

    /**
     * Tests if it throws an InvalidContentException when no content is sent with the request.
     *
     * @throws InvalidContentException     When the content type is not in json.
     * @throws InvalidContentTypeException When the content type is not "application/json".
     *
     * @return void
     */
    public function testGetRequestInvalidContent(): void
    {
        $this->expectException(InvalidContentException::class);
        $request = new Request([], [], [], [], [], ['CONTENT_TYPE' => ['application/json']], null);

        $validator = $this->createMock(ValidatorInterface::class);
        $argumentResolver = new BaseArgumentResolver($validator);
        $argumentResolver->getRequestContent($request);
    }

    /**
     * Tests if the validate function throws an exception when there are constraints.
     *
     * @return void
     *
     * @throws ValidationException When the validation fails.
     */
    public function testValidate(): void
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $violations = new ConstraintViolationList([]);
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn($violations);
        $argumentResolver = new BaseArgumentResolver($validator);
        $argumentResolver->validate([]);
    }

    /**
     * Tests if the validate function throws an exception when there are constraints.
     *
     * @return void
     *
     * @throws ValidationException When the validation fails.
     */
    public function testValidateException(): void
    {
        $this->expectException(ValidationException::class);

        $validator = $this->createMock(ValidatorInterface::class);
        $violations = new ConstraintViolationList(array(
            $this->createMock(ConstraintViolation::class),
        ));
        $validator->expects($this->once())
            ->method('validate')
            ->willReturn($violations);
        $argumentResolver = new BaseArgumentResolver($validator);
        $argumentResolver->validate([]);
    }

    /**
     * Test if the rules are returned.
     *
     * @return void
     */
    public function testValidationGroup(): void
    {
        $validator = $this->createMock(TraceableValidator::class);
        $argumentResolver = new BaseArgumentResolver($validator);

        $this->assertEquals(new Assert\GroupSequence(['Default']), $argumentResolver->validationGroup());
    }

}
