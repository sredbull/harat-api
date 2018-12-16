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

use App\Response\User\GetProfileResponse;
use App\Response\User\GetUsersResponse;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 */
class UserController extends AbstractController
{

    /**
     * Get all users.
     *
     * @param UserService $userService The user service.
     *
     * @Route("/user", methods={"GET"})
     *
     * @return GetUsersResponse
     */
    public function getUsers(UserService $userService): GetUsersResponse
    {
        return GetUsersResponse::get($userService->getAllUsers());
    }

    /**
     * Get current profile.
     *
     * @param UserService $userService The user service.
     *
     * @Route("/user/profile", methods={"GET"})
     *
     * @return GetProfileResponse
     */
    public function getProfile(UserService $userService): GetProfileResponse
    {
        return GetProfileResponse::get($userService->getProfile());
    }

}
