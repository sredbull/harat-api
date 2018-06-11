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
use App\ParamConverter\Login\PostLoginParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\ConstraintViolationListInterface;

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
     * @param PostLoginParamConverter          $params           The validated login fields.
     * @param ConstraintViolationListInterface $validationErrors The validation validation errors.
     *
     * @Rest\Post("login")
     *
     * @ParamConverter("params", converter="fos_rest.request_body")
     *
     * @return void
     *
     * @throws ValidationException Thrown when the registration login fails.
     * @throws \DomainException    Should never be thrown because the response is handled by the LDAP service.
     */
    public function postAction(PostLoginParamConverter $params, ConstraintViolationListInterface $validationErrors): void
    {
        if (count($validationErrors) > 0) {
            throw new ValidationException($validationErrors);
        }

        throw new \DomainException(sprintf('%s, this should be handled by LDAP', $params->getUsername()));
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
