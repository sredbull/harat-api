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

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController.
 */
class UserController extends BaseController
{

    /**
     * List all users.
     *
     * @Rest\Get("user")
     *
     * @return JsonResponse
     */
    public function getUsers(): JsonResponse
    {
        $users = $this->getRepository() !== null ? $this->getRepository()->findAll() : null;

        return $this->getView($users, Response::HTTP_OK);
    }

    /**
     * List current profile.
     *
     * @Rest\Get("user/profile")
     *
     * @return JsonResponse
     */
    public function getProfile(): JsonResponse
    {
        $user = $this->getUser();
        $userDetails = null;

        if ($this->getRepository() !== null) {
            $userDetails = $this->getRepository()->findOneBy([
                'username' => $user->getUsername(),
            ]);
        }

        return $this->getView($userDetails, Response::HTTP_OK);
    }

}
