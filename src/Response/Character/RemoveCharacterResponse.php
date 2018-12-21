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
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Response(
 *     response="RemoveCharacterResponse",
 *     description="successful operation",
 *     @OA\JsonContent()
 * )
 */
class RemoveCharacterResponse extends BaseResponse
{

    public const HTTP_CODE = Response::HTTP_NO_CONTENT;

    /**
     * Get the response.
     *
     * @return self
     */
    public static function get(): self
    {
        $response = new self(self::HTTP_CODE);
        $response->setData('');

        return $response;
    }

}
