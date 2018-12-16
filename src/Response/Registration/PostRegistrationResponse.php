<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Response\Registration;

use App\Response\BaseResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Response(
 *     response="PostRegistrationResponse",
 *     description="successful operation",
 *     @OA\JsonContent(
 *         @OA\Property(property="message", type="string")
 *     )
 * )
 */
class PostRegistrationResponse extends BaseResponse
{

    /**
     * Get the response.
     *
     * @return self
     */
    public static function get(): self
    {
        $response = new self();
        $response->setData(['message' => 'User registered']);

        return $response;
    }

}
