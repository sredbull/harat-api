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

use App\Response\BaseResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Response(
 *     response="PostLoginResponse",
 *     description="successful operation",
 *     @OA\JsonContent(
 *         @OA\Property(property="message", type="string"),
 *         @OA\Property(property="token", type="string")
 *     )
 * )
 */
class PostLoginResponse extends BaseResponse
{

    /**
     * Get the response.
     *
     * @param string $token The token.
     *
     * @return PostLoginResponse
     */
    public static function get(string $token): self
    {
        $response = new self();

        $response->setData([
            'message' => 'Login successful',
            'token' => $token,
        ]);

        return $response;
    }

}
