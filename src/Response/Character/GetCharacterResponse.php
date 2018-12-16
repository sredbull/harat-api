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

use App\Entity\CharacterEntity;
use App\Response\BaseResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Response(
 *     response="GetCharacterResponse",
 *     description="successful operation",
 *     @OA\JsonContent(ref="#/components/schemas/CharacterEntity")
 * )
 */
class GetCharacterResponse extends BaseResponse
{

    /**
     * Get the response.
     *
     * @param CharacterEntity $character The character.
     *
     * @return self
     */
    public static function get(CharacterEntity $character): self
    {
        $response = new self();
        $response->setData($character);

        return $response;
    }

}
