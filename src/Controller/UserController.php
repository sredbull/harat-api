<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * The UserController Class.
 *
 * @Rest\RouteResource("User", pluralize=false)
 */
class UserController extends FOSRestController implements ClassResourceInterface
{
    /**
     * The Doctrine entity manager.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * The userRepository.
     *
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserController constructor.
     *
     * @param EntityManagerInterface    $entityManager  The Doctrine entity manager.
     * @param UserRepository            $userRepository The userRepository.
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
     * @return Response
     * @throws NotFoundHttpException
     */
    public function getAction(string $userId): Response
    {
        $ldap = $this->get('ldap_tools.ldap_manager');
        $users = $ldap->buildLdapQuery()->fromUsers()->getLdapQuery()->getResult();

        var_dump($users);
        die;


        $user = $this->userRepository->find($userId);

        if (null === $user) {
            throw new NotFoundHttpException();
        }

        return $this->handleView(
            $this->view(
                $user,
                Response::HTTP_OK
            )
        );
    }

    /**
     * Creates an user.
     *
     * @param Request $request The original request.
     *
     * @return View
     */
    public function postAction(Request $request) : View
    {
        $form = $this->createForm(UserType::class, new User());
        $form->submit($request->request->all());

        if (false === $form->isValid()) {
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
     * @param Request $request  The original request.
     * @param string  $userId   The id of the user.
     *
     * @return View
     * @throws NotFoundHttpException When the user could not be found.
     */
    public function patchAction(request $request, string $userId) : View
    {
        $existingUser = $this->userRepository->find($userId);

        if (null === $existingUser) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(UserType::class, $existingUser);
        $form->submit($request->request->all());

        if (false === $form->isValid()) {
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
     * @throws NotFoundHttpException When the user could not be found.
     */
    public function deleteAction(string $userId) : View
    {
        $existingUser = $this->userRepository->find($userId);

        if (null === $existingUser) {
            throw new NotFoundHttpException();
        }

        $this->entityManager->remove($existingUser);
        $this->entityManager->flush();

        return $this->view(
            null,
            Response::HTTP_NO_CONTENT
        );
    }
}
