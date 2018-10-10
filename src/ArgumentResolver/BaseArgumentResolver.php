<?php /** @noinspection MessDetectorValidationInspection */
declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\ArgumentResolver;

use App\Exception\InvalidContentException;
use App\Exception\InvalidContentTypeException;
use App\Exception\ValidationException;
use App\Interfaces\ArgumentResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * The BaseArgumentResolver Class.
 */
class BaseArgumentResolver implements ArgumentResolverInterface
{

    /**
     * The validator interafce.
     *
     * @var ValidatorInterface $validator
     */
    private $validator;

    /**
     * BaseArgumentResolver constructor.
     *
     * @param ValidatorInterface $validator The validation interface.
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Checks if the class supports to resolve its arguments.
     *
     * @param Request          $request  The request.
     * @param ArgumentMetadata $argument The argument meta data.
     *
     * @return boolean
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return false;
    }

    /**
     * Resolve the request.
     *
     * @param Request          $request  The request.
     * @param ArgumentMetadata $argument The argument meta data.
     *
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        yield $this;
    }

    /**
     * Set the rules for this request.
     *
     * @return Assert\Collection
     */
    public function rules(): Assert\Collection
    {
        return new Assert\Collection([]);
    }

    /**
     * Get the request content.
     *
     * @param Request $request The request.
     *
     * @return array
     *
     * @throws InvalidContentTypeException When the content type is not json.
     * @throws InvalidContentException     When the content is not valid.
     */
    public function getRequestContent(Request $request): array
    {
        if ($request->getContentType() !== 'json') {
            throw new InvalidContentTypeException(sprintf('Invalid content type: "%s" posted, expected: "json"', $request->getContentType()));
        }

        $json = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidContentException(sprintf('Invalid content: %s', json_last_error_msg()));
        }

        return $json;
    }

    /**
     * Validate the request.
     *
     * @param array $data The data to validate.
     *
     * @throws ValidationException When validation fails.
     *
     * @return void
     */
    public function validate(array $data): void
    {
        $validationErrors = $this->validator->validate($data, $this->rules(), $this->validationGroup());

        if (count($validationErrors) !== 0) {
            throw new ValidationException($validationErrors);
        }
    }

    /**
     * Set the validation sequence.
     *
     * @return Assert\GroupSequence
     */
    public function validationGroup(): Assert\GroupSequence
    {
        return new Assert\GroupSequence(['Default']);
    }

}
