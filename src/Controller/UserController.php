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

use App\Entity\UserEntity;
use App\Exception\UserNotFoundException;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController.
 *
 * @Rest\RouteResource("User", pluralize=false)
 */
class UserController extends FOSRestController implements ClassResourceInterface
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
     * List all users.
     *
     * @return Response
     */
    public function cgetAction(): Response
    {
        $users = $this->userRepository->findall();

        return $this->handleView(
            $this->view(
                $users,
                Response::HTTP_OK
            )
        );
    }

    /**
     * List an user.
     *
     * @param string $userId The id of the user to retrieve.
     *
     * @return View
     *
     * @throws UserNotFoundException Thrown when the user could not be found.
     */
    public function getAction(string $userId): View
    {
        $user = $this->userRepository->find($userId);

        if ($user === null) {
            throw new UserNotFoundException();
        }

        return $this->view(
            $user,
            Response::HTTP_OK
        );
    }

    /**
     * Creates an user.
     *
     * @param Request $request The original request.
     *
     * @return View
     */
    public function postAction(Request $request): View
    {
        $form = $this->createForm(UserType::class, new UserEntity());
        $form->submit($request->request->all());

        if ($form->isValid() === false) {
            return $this->view($form);
        }

        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        return $this->view(
            [
                'status' => 'ok',
            ],
            Response::HTTP_OK
        );
    }

    /**
     * Updates an user.
     *
     * @param Request $request The original request.
     * @param string  $userId  The id of the user.
     *
     * @return View
     *
     * @throws UserNotFoundException Thrown when the user could not be found.
     */
    public function patchAction(Request $request, string $userId): View
    {
        $existingUser = $this->userRepository->find($userId);

        if ($existingUser === null) {
            throw new UserNotFoundException();
        }

        $form = $this->createForm(UserType::class, $existingUser);
        $form->submit($request->request->all());

        if ($form->isValid() === false) {
            return $this->view($form);
        }

        $this->entityManager->flush();

        return $this->view(
            null,
            Response::HTTP_NO_CONTENT
        );
    }

    /**
     * Deletes an user.
     *
     * @param string $userId The user id to delete.
     *
     * @return View
     *
     * @throws UserNotFoundException Thrown when the user could not be found.
     */
    public function deleteAction(string $userId): View
    {
        $existingUser = $this->userRepository->find($userId);

        if ($existingUser === null) {
            throw new UserNotFoundException();
        }

        $this->entityManager->remove($existingUser);
        $this->entityManager->flush();

        return $this->view(
            null,
            Response::HTTP_NO_CONTENT
        );
    }

}
