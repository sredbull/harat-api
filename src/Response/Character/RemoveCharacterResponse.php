<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Response\Character;

use App\Exception\ApiException;
use App\Response\BaseResponse;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Response(
 *     response="RemoveCharacterResponse",
 *     description="successful operation",
 *     @OA\JsonContent(ref="#/components/schemas/CharacterEntity")
 * )
 */
class RemoveCharacterResponse extends BaseResponse
{

    public const HTTP_CODE = Response::HTTP_NO_CONTENT;

    /**
     * Get the response.
     *
     * @throws ApiException When the includes passed are not array values.
     *
     * @return self
     */
    public static function get(): self
    {
        return new self();
    }

}
