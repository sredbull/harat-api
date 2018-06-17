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
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProfileController.
 *
 * @Rest\RouteResource("Profile", pluralize=false)
 */
class ProfileController extends FOSRestController implements ClassResourceInterface
{

    /**
     * The Doctrine entity manager.
     *
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * The userRepository.
     *
     * @var UserRepository $userRepository
     */
    private $userRepository;

    /**
     * UserController constructor.
     *
     * @param EntityManagerInterface $entityManager  The Doctrine entity manager.
     * @param UserRepository         $userRepository The userRepository.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ){
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * Get the profile of the current logged in user.
     *
     * @Rest\Get("profile")
     *
     * @return View
     */
    public function getAction(): View
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
