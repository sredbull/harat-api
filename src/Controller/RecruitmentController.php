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
use App\Service\RecruitmentService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The RecruitmentController Class.
 */
class RecruitmentController extends BaseController
{

    /**
     * Get a recruitment.
     *
     * @param RecruitmentEntity|null $recruitment The user the recruitment belongs to.
     *
     * @Route("/recruitment/{id}", methods={"GET"})
     *
     * @return JsonResponse
     *
     * @throws RecruitmentNotFoundException When the recruitment could not be found.
     */
    public function getRecruitment(?RecruitmentEntity $recruitment): JsonResponse
    {
        if ($recruitment === null) {
            throw new RecruitmentNotFoundException();
        }

        return $this->view($recruitment, Response::HTTP_OK);
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
     * @Route("/recruitment/{id}", methods={"POST"})
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
