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

use App\Exception\ApiException;
use App\ParamConverter\Login\PostLoginRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The LoginController Class.
 */
class LoginController extends BaseController
{

    /**
     * Login route.
     *
     * @param PostLoginRequest $request The request.
     *
     * @Route("/login", methods={"POST, GET"})
     *
     * @return void
     *
     * @throws ApiException When Something is wrong with the ldap server.
     */
    public function postAction(PostLoginRequest $request): void
    {
        throw new ApiException(sprintf('%s, this should be handled by LDAP', $request->getUsername()), Response::HTTP_INTERNAL_SERVER_ERROR);
    }

}
