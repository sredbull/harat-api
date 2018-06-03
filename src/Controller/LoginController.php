<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;

/**
 * The LoginController Class.
 *
 * @package App\Controller
 *
 * @Rest\RouteResource("Login", pluralize=false)
 */
class LoginController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Login route.
     */
    public function postAction()
    {
        throw new \DomainException('Handled by JWT');
    }

    /**
     * @Rest\Get("login", name="login")
     */
    public function loginAction()
    {
        throw new \DomainException('Handled by LDAP');
    }
}
