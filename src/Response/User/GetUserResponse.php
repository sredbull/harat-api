<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Response\User;

use App\Entity\UserEntity;
use App\Response\BaseResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Response(
 *     response="GetUserResponse",
 *     description="successful operation",
 *     @OA\JsonContent(ref="#/components/schemas/UserEntity")
 * )
 */
class GetUserResponse extends BaseResponse
{

    /**
     * Get the response.
     *
     * @param UserEntity $user The user to return.
     *
     * @return self
     */
    public static function get(UserEntity $user): self
    {
        $response = new self();

        $response->setData($user, ['user']);

        return $response;
    }

}
