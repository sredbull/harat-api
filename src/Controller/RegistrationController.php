<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The RegistrationController Class.
 *
 * @Rest\RouteResource("Register", pluralize=false)
 */
class RegistrationController extends FOSRestController implements ClassResourceInterface
{
    /**
     * The user manager interface.
     *
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * RegistrationController constructor.
     *
     * @param UserManagerInterface $userManager The user manager interface.
     */
    public function __construct(
        UserManagerInterface $userManager
    ){
        $this->userManager = $userManager;
    }

    /**
     * Register a new user.
     *
     * @param Request $request The request containing the user data.
     *
     * @return View
     */
    public function postAction(Request $request) : View
    {
        $formFactory = $this->get('registration_form');

        $user = $this->userManager->createUser();
        $user->setEnabled(true);

        $form = $formFactory->createForm([
            'csrf_protection'    => false
        ]);
        $form->setData($user);
        $form->submit($request->request->all());

        if (false === $form->isValid()) {
            return $this->view($form);
        }

        $this->userManager->updateUser($user);

        return $this->view(
            [
                'msg' => $this->get('translator')
                    ->trans('registration.flash.user_created', [], 'FOSUserBundle'),
                'token' => $this->get('lexik_jwt_authentication.jwt_manager')
                    ->create($user),
                'data' => $user
            ],
            Response::HTTP_CREATED
        );
    }
}