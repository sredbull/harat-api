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

use App\Exception\ValidationException;
use App\ParamConverter\Registration\PostRegistrationParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Model\UserManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class RegistrationController.
 *
 * @Rest\RouteResource("Register", pluralize=false)
 */
class RegistrationController extends FOSRestController implements ClassResourceInterface
{

    /**
     * The user manager interface.
     *
     * @var UserManagerInterface $userManager
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
     * @param PostRegistrationParamConverter   $params           The validated registration fields.
     * @param ConstraintViolationListInterface $validationErrors The validation validation errors.
     *
     * @Rest\Post("register")
     *
     * @ParamConverter("params", converter="fos_rest.request_body")
     *
     * @return View
     *
     * @throws ValidationException Thrown when the registration validation fails.
     */
    public function postAction(PostRegistrationParamConverter $params, ConstraintViolationListInterface $validationErrors): View
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        dump($params->getUsername());

//        $formFactory = $this->get('registration_form');
//
//        $user = $this->userManager->createUser();
//        $user->setEnabled(true);
//
//        $form = $formFactory->createForm([
//            'csrf_protection'    => false,
//        ]);
//        $form->setData($user);
//        $form->submit($request->request->all());
//
//        if ($form->isValid() === false) {
//            return $this->view($form);
//        }
//
//        $this->userManager->updateUser($user);

        return $this->view(
            [
                'status' => 'ok',
            ],
            Response::HTTP_CREATED
        );
    }

}

