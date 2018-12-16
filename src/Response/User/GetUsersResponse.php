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

use App\Response\BaseResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Response(
 *     response="GetUsersResponse",
 *     description="successful operation",
 *     @OA\JsonContent(
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/UserEntity")
 *     ),
 * )
 */
class GetUsersResponse extends BaseResponse
{

    /**
     * Get the response.
     *
     * @param array $users The users to return.
     *
     * @return self
     */
    public static function get(array $users): self
    {
        $response = new self();

        $response->setData($users, ['user']);

        return $response;
    }

}
