<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Response\Login;

use App\Exception\ApiException;
use App\Response\BaseResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Response(
 *     response="GetRefreshResponse",
 *     description="successful operation",
 *     @OA\JsonContent(
 *         @OA\Property(property="message", type="string"),
 *         @OA\Property(property="token", type="string")
 *     )
 * )
 */
class GetRefreshResponse extends BaseResponse
{

    /**
     * Get the response.
     *
     * @param string $token The token.
     *
     * @return GetRefreshResponse
     *
     * @throws ApiException When the includes passed are not array values.
     */
    public static function get(string $token): self
    {
        $response = new self();

        $response->setData([
            'message' => 'Refresh successful',
            'token' => $token,
        ]);

        return $response;
    }

}
