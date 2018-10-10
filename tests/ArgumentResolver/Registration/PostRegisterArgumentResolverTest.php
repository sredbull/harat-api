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

use App\ArgumentResolver\Registration\PostRegisterArgumentResolver;
use App\Exception\InvalidContentException;
use App\Exception\InvalidContentTypeException;
use App\Exception\ValidationException;
use App\Tests\BaseTestCase;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\TraceableValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class PostRegisterArgumentResolverTest.
 */
class PostRegisterArgumentResolverTest extends BaseTestCase
{

    /**
     * Test if supports returns true when the class is supported.
     *
     * @return void
     */
    public function testSupportsIsTrue(): void
    {
        $request = new Request([], [], [], [], [], [], null);
        $validator = $this->createMock(TraceableValidator::class);
        $argumentMetadata = new ArgumentMetadata('resolver', PostRegisterArgumentResolver::class, false, false, null);
        $argumentResolver = new PostRegisterArgumentResolver($validator);

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
        $validator = $this->createMock(TraceableValidator::class);
        $argumentMetadata = new ArgumentMetadata('resolver', 'App\Class\NotSupported', false, false, null);
        $argumentResolver = new PostRegisterArgumentResolver($validator);

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
            'email' => 'roodbol.sven@gmail.com',
            'username' => 'username',
            'password' => [
                'first' => 'password',
                'second' => 'password',
            ],
        ];

        $rules = new Assert\Collection([
            'email' => [
                new Assert\NotBlank(['groups' => 'first']),
                new Assert\Type(['type' => 'string', 'groups' => 'first']),
                new Assert\Email(['checkMX' => true, 'checkHost' => true, 'groups' => 'second', 'mode' => 'strict']),
                new AppAssert\ExistingLdapUser(['type' => 'email', 'groups' => 'third']),
            ],
            'username' => [
                new Assert\NotBlank(['groups' => 'first']),
                new Assert\Type(['type' => 'string', 'groups' => 'second']),
                new Assert\Length(['min' => 3, 'groups' => 'second']),
                new AppAssert\ExistingLdapUser(['type' => 'username', 'groups' => 'third']),
            ],
            'password' => [
                new Assert\NotBlank(['groups' => 'first']),
                new Assert\Type(['type' => 'array', 'groups' => 'second']),
                new Assert\Collection([
                    'first' => [
                        new Assert\NotBlank(['groups' => 'third']),
                        new Assert\Type(['type' => 'string', 'groups' => 'third']),
                        new AppAssert\MatchingPassword(['groups' => 'fourth', 'propertyPath' => '[password][second]']),
                    ],
                    'second' => [
                        new Assert\NotBlank(['groups' => 'third']),
                        new Assert\Type(['type' => 'string', 'groups' => 'third']),
                    ],
                ]),
            ],
        ]);

        $group = new Assert\GroupSequence(['first', 'second', 'third', 'fourth']);

        $request = new Request([], [], [], [], [], ['CONTENT_TYPE' => ['application/json']], json_encode($data));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->with($data, $rules, $group)
            ->willReturn([]);
        $argumentMetadata = new ArgumentMetadata('resolver', PostRegisterArgumentResolver::class, false, false, null);
        $argumentResolver = new PostRegisterArgumentResolver($validator);
        $request = $argumentResolver->resolve($request, $argumentMetadata);

        $this->assertEquals('roodbol.sven@gmail.com', $request->current()->getEmail());
        $this->assertEquals('username', $request->current()->getUsername());
        $this->assertEquals('password', $request->current()->getPassword());

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
        $argumentMetadata = new ArgumentMetadata('resolver', PostRegisterArgumentResolver::class, false, false, null);
        $argumentResolver = new PostRegisterArgumentResolver($validator);
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
        $argumentMetadata = new ArgumentMetadata('resolver', PostRegisterArgumentResolver::class, false, false, null);
        $argumentResolver = new PostRegisterArgumentResolver($validator);
        $request = $argumentResolver->resolve($request, $argumentMetadata);
        $request->current();
    }

}
