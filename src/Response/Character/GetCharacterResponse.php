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

use App\Response\BaseResponse;
use OpenApi\Annotations as OA;

/**
 * Class GetCharacterResponse
 *
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
     * @param mixed $data The data to return.
     *
     * @return self
     */
    public function getResponse($data): self
    {
        $this->setData($data);

        return $this;
    }

}

