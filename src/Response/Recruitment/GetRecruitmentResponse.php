<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Response\Recruitment;

use App\Entity\RecruitmentEntity;
use App\Response\BaseResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Response(
 *     response="GetRecruitmentResponse",
 *     description="successful operation",
 *     @OA\JsonContent(ref="#/components/schemas/RecruitmentEntity")
 * )
 */
class GetRecruitmentResponse extends BaseResponse
{

    /**
     * Get the response.
     *
     * @param RecruitmentEntity $recruitment The data to return.
     *
     * @return self
     */
    public static function get(RecruitmentEntity $recruitment): self
    {
        $response = new self();

        $response->setData($recruitment);

        return $response;
    }

}
