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

use App\Exception\RegistrationFailedException;
use App\Exception\ValidationException;
use App\ParamConverter\Registration\PostRegistrationParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Model\UserManagerInterface;
use LdapTools\Exception\LdapConnectionException;
use LdapTools\LdapManager;
use LdapTools\Object\LdapObjectType;
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
     * The ldap manager.
     *
     * @var LdapManager $ldapManager
     */
    private $ldapManager;

    /**
     * The user manager interface.
     *
     * @var UserManagerInterface $userManager
     */
    private $userManager;

    /**
     * RegistrationController constructor.
     *
     * @param LdapManager          $ldapManager The LDAP manager.
     * @param UserManagerInterface $userManager The user manager interface.
     */
    public function __construct(
        LdapManager $ldapManager,
        UserManagerInterface $userManager
    ){
        $this->ldapManager = $ldapManager;
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
     * @throws ValidationException         Thrown when the registration validation fails.
     * @throws RegistrationFailedException Thrown when the registration fails for some reason.
     */
    public function postAction(PostRegistrationParamConverter $params, ConstraintViolationListInterface $validationErrors): View
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        try {
            $this->ldapManager
                ->createLdapObject()
                ->createUser()
                ->in('ou=people,dc=housearatus,dc=space')
                ->with([
                    'email' => $params->getEmail(),
                    'name' => $params->getUsername(),
                    'password' => $params->getPassword()['first'],
                    'username' => $params->getUsername(),
                    'uid' => $params->getUsername(),
                ])
                ->execute();
        } catch (LdapConnectionException $exception) {
            throw new RegistrationFailedException($exception->getMessage());
        }

        return $this->view(
            [
                'code' => Response::HTTP_CREATED,
                'status' => 'ok',
            ],
            Response::HTTP_CREATED
        );
    }

}

