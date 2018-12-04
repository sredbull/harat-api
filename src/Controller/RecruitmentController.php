<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Controller;

use App\ArgumentResolver\Recruitment\PostRecruitmentArgumentResolver;
use App\Entity\RecruitmentEntity;
use App\Entity\UserEntity;
use App\Exception\DatabaseException;
use App\Exception\RecruitmentNotFoundException;
use App\Exception\UserNotFoundException;
use App\Response\Recruitment\GetRecruitmentResponse;
use App\Response\Recruitment\GetRecruitmentsResponse;
use App\Response\Recruitment\PostRecruitmentResponse;
use App\Service\RecruitmentService;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The RecruitmentController Class.
 */
class RecruitmentController extends BaseController
{

    /**
     * Get a recruitment.
     *
     * @param RecruitmentEntity      $recruitment The recruitment id.
     * @param GetRecruitmentResponse $response    The response object.
     *
     * @Route("/recruitment/{recruitment}", methods={"GET"})
     *
     * @return GetRecruitmentResponse
     *
     * @throws RecruitmentNotFoundException When the recruitment could not be found.
     *
     * @OA\SecurityScheme(
     *     securityScheme="api_key",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT"
     * )
     */
    public function getRecruitment(?RecruitmentEntity $recruitment, GetRecruitmentResponse $response): GetRecruitmentResponse
    {
        if ($recruitment === null) {
            throw new RecruitmentNotFoundException();
        }

        return $response->getResponse($recruitment);
    }

    /**
     * Get all recruitments.
     *
     * @param RecruitmentService      $recruitmentService The recruitment service.
     * @param GetRecruitmentsResponse $response           The response object.
     *
     * @Route("/recruitment", methods={"GET"})
     *
     * @return GetRecruitmentsResponse
     */
    public function getRecruitments(RecruitmentService $recruitmentService, GetRecruitmentsResponse $response): GetRecruitmentsResponse
    {
        return $response->getResponse($recruitmentService->getAllRecruitments());
    }

    /**
     * Post a recruitment.
     *
     * @param RecruitmentService              $recruitmentService The recruitment service.
     * @param UserEntity|null                 $user               The user the recruitment belongs to.
     * @param PostRecruitmentArgumentResolver $request            The validated recruitment request.
     * @param PostRecruitmentResponse         $response           The response object.
     *
     * @Route("/recruitment/{user}", methods={"POST"})
     *
     * @return PostRecruitmentResponse
     *
     * @throws DatabaseException     When saving or removing a recruitment fails.
     * @throws UserNotFoundException When the user could not be found.
     */
    public function postRecruitment(RecruitmentService $recruitmentService, ?UserEntity $user, PostRecruitmentArgumentResolver $request, PostRecruitmentResponse $response): PostRecruitmentResponse
    {
        if ($user === null) {
            throw new UserNotFoundException();
        }

        $recruitmentService->deleteRecruitmentsFromUser($user);
        $recruitmentService->newRecruitmentForUser($user, $request->getForm());

        return $response->getResponse();
    }

}
