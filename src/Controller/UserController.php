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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 */
class UserController extends BaseController
{

    /**
     * List all users.
     *
     * @Route("/user", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getUsers(): JsonResponse
    {
        return $this->serve(new GetUsersFeature());
    }

    /**
     * List current profile.
     *
     * @Route("/user/profile", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function getProfile(): JsonResponse
    {
        return $this->serve(new GetProfileFeature());
    }

}
