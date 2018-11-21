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
use App\Response\Recruitment\RecruitmentResponse;
use App\Service\RecruitmentService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * The RecruitmentController Class.
 */
class RecruitmentController extends BaseController
{

    /**
     * Get a recruitment.
     *
     * Dit is een description wat meestal wat langer is.
     *
     * @param RecruitmentEntity $recruitment The recruitment id.
     *
     * @Route("/recruitment/{recruitment}", methods={"GET"})
     *
     * @return RecruitmentResponse
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
    public function getRecruitment(?RecruitmentEntity $recruitment, RecruitmentResponse $response): RecruitmentResponse
    {
        if ($recruitment === null) {
            throw new RecruitmentNotFoundException();
        }

        return $response->getResponse($recruitment);
    }

    /**
     * Get all recruitments.
     *
     * @param RecruitmentService $recruitmentService The recruitment service.
     *
     * @Route("/recruitment", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getRecruitments(RecruitmentService $recruitmentService): JsonResponse
    {
        return $this->view($recruitmentService->getAllRecruitments(), Response::HTTP_OK);
    }

    /**
     * Post a recruitment.
     *
     * @param RecruitmentService              $recruitmentService The recruitment service.
     * @param UserEntity|null                 $user               The user the recruitment belongs to.
     * @param PostRecruitmentArgumentResolver $request            The validated recruitment request.
     *
     * @Route("/recruitment/{$user}", methods={"POST"})
     *
     * @return JsonResponse
     *
     * @throws DatabaseException     When saving or removing a recruitment fails.
     * @throws UserNotFoundException When the user could not be found.
     */
    public function postRecruitment(RecruitmentService $recruitmentService, ?UserEntity $user, PostRecruitmentArgumentResolver $request): JsonResponse
    {
        if ($user === null) {
            throw new UserNotFoundException();
        }

        $recruitmentService->deleteRecruitmentsFromUser($user);
        $recruitmentService->newRecruitmentForUser($user, $request->getForm());

        return $this->view(['message' => 'Recruitment posted'], Response::HTTP_OK);
    }

}
