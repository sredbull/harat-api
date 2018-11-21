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
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RecruitmentResponse
 *
 * @OA\Response(
 *     response="RecruitmentResponse",
 *     description="successful operation"
 * )
 */
class RecruitmentResponse extends BaseResponse
{

    /**
     * The response code.
     *
     * @var int $httpCode
     */
    static public $httpCode = Response::HTTP_OK;

    /**
     * Get the response.
     *
     * @param mixed $data The data to return.
     *
     * @return self
     */
    public function getResponse($data): self
    {
        $this->setStatusCode(self::$httpCode);
        $this->setData($data);

        return $this;
    }

}

