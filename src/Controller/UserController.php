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

use App\Repository\UserRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController.
 */
class UserController extends FOSRestController
{

    /**
     * The userRepository.
     *
     * @var UserRepository $userRepository
     */
    private $userRepository;

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepository The userRepository.
     */
    public function __construct(
        UserRepository $userRepository
    ){
        $this->userRepository = $userRepository;
    }

    /**
     * List all users.
     *
     * @Rest\Get("user")
     *
     * @return View
     */
    public function getUsers(): View
    {
        $users = $this->userRepository->findAll();

        return $this->view(
            $users,
            Response::HTTP_OK
        );
    }

    /**
     * List all users.
     *
     * @Rest\Get("user/profile")
     *
     * @return View
     */
    public function getProfile(): View
    {
        $user = $this->getUser();
        $userDetails = $this->userRepository->findOneBy([
            'username' => $user->getUsername(),
        ]);

        return $this->view(
            $userDetails,
            Response::HTTP_OK
        );
    }

}
