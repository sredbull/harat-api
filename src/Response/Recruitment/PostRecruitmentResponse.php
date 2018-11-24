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

use App\Response\BaseResponse;
use OpenApi\Annotations as OA;

/**
 * Class PostRecruitmentResponse
 *
 * @OA\Response(
 *     response="PostRecruitmentResponse",
 *     description="successful operation"
 * )
 */
class PostRecruitmentResponse extends BaseResponse
{

    /**
     * Get the response.
     *
     * @return self
     */
    public function getResponse(): self
    {
        $this->setData(['message' => 'Recruitment posted']);

        return $this;
    }

}

