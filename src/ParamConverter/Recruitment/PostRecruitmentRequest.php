<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\ParamConverter\Recruitment;

use App\Interfaces\RequestObjectInterface;
use Fesor\RequestObject\RequestObject;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PostRecruitmentRequest.
 */
class PostRecruitmentRequest extends RequestObject implements RequestObjectInterface
{

    /**
     * The recruitment form.
     *
     * @var array $form
     */
    private $form;

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
