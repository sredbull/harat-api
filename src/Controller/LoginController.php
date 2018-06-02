<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;

/**
 * The LoginController Class.
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
}
