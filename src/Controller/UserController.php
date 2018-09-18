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

use App\Features\User\GetProfileFeature;
use App\Features\User\GetUsersFeature;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 */
class UserController extends BaseController
{

    /**
     * Get all users.
     *
     * @param UserService $userService The user service.
     *
     * @Route("/user", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getUsers(UserService $userService): JsonResponse
    {
        return $this->view($userService->getAllUsers(), Response::HTTP_OK);
    }

    /**
     * Get current profile.
     *
     * @param UserService $userService The user service.
     *
     * @Route("/user/profile", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getProfile(UserService $userService): JsonResponse
    {
        return $this->view($userService->getProfile(), Response::HTTP_OK);
    }

}
