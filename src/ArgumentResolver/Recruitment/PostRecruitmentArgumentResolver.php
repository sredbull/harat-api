<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\ArgumentResolver\Recruitment;

use App\ArgumentResolver\BaseArgumentResolver;
use App\Exception\InvalidContentException;
use App\Exception\InvalidContentTypeException;
use App\Exception\ValidationException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @OA\Schema(schema="PostRecruitmentArgumentResolver")
 */
class PostRecruitmentArgumentResolver extends BaseArgumentResolver
{

    /**
     * The recruitment form.
     *
     * @var array $form
     *
     * @OA\Property(
     *     @OA\Items(
     *         type="string"
     *     )
     * )
     */
    private $form;

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
        return $argument->getType() === PostRecruitmentArgumentResolver::class;
    }

    /**
     * Resolve the request.
     *
     * @param Request          $request  The request.
     * @param ArgumentMetadata $argument The argument meta data.
     *
     * @return \Generator
     *
     * @throws InvalidContentException     When invalid content is detected.
     * @throws InvalidContentTypeException When the content type is invlaid.
     * @throws ValidationException         When validation fails.
     */
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $content = $this->getRequestContent($request);
        $this->validate($content);

        $this->form = $content['form'];

        yield $this;
    }

    /**
     * Set the rules for this request.
     *
     * @return Assert\Collection
     */
    public function rules(): Assert\Collection
    {
        return new Assert\Collection([
            'form' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'array']),
            ],
        ]);
    }

    /**
     * Get the form.
     *
     * @return array
     */
    public function getForm(): array
    {
        return $this->form;
    }

}
