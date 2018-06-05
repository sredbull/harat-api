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
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;

/**
 * The LoginController Class.
 *
 * @Rest\RouteResource("Login", pluralize=false)
 */
class LoginController extends FOSRestController implements ClassResourceInterface
{

    /**
     * Login route.
     *
     * @return void
     *
     * @throws \DomainException Should never be thrown because the response is handled by the LDAP service.
     */
    public function postAction(): void
    {
        throw new \DomainException('Handled by LDAP');
    }

    /**
     * Login route.
     *
     * @Rest\Get("login", name="login")
     *
     * @return void
     *
     * @throws \DomainException Should never be thrown because the response is handled by the LDAP service.
     */
    public function loginAction(): void
    {
        throw new \DomainException('Handled by LDAP');
    }

}
