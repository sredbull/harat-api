<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Interfaces;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The ArgumentResolverInterface Class.
 */
interface ArgumentResolverInterface extends ArgumentValueResolverInterface
{

    /**
     * Set the rules for this request.
     *
     * @return Assert\Collection
     */
    public function rules(): Assert\Collection;

    /**
     * Get the request content.
     *
     * @param Request $request The request.
     *
     * @return array.
     */
    public function getRequestContent(Request $request): array;

    /**
     * Validate the request.
     *
     * @param array $data The data to validate.
     *
     * @return void
     */
    public function validate(array $data): void;

    /**
     * Set the validation sequence.
     *
     * @return Assert\GroupSequence
     */
    public function validationGroup(): Assert\GroupSequence;

}
