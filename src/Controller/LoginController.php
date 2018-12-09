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

use App\ArgumentResolver\Login\PostLoginArgumentResolver;
use App\Exception\ApiException;
use App\Exception\AuthenticationFailedException;
use App\Exception\DatabaseException;
use App\Exception\InvalidTokenException;
use App\Exception\TokenNotFoundException;
use App\Response\Login\GetRefreshResponse;
use App\Response\Login\PostLoginResponse;
use App\Service\AuthenticationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends BaseController
{

    /**
     * Refresh route.
     *
     * @param Request               $request               The request.
     * @param AuthenticationService $authenticationService The authentication service.
     *
     * @Route("/login/refresh", methods={"GET"})
     *
     * @return GetRefreshResponse
     *
     * @throws ApiException           When the includes passed are not array values.
     * @throws TokenNotFoundException When the token was not found in the Authorization header.
     * @throws DatabaseException      When the refresh token could not be saved.
     * @throws InvalidTokenException  When the token appears to be invalid or expired.
     */
    public function getRefresh(Request $request, AuthenticationService $authenticationService): GetRefreshResponse
    {
        $currentToken = str_replace('Bearer ', '', $request->headers->get('Authorization'));
        if($currentToken === null) {
            throw new TokenNotFoundException();
        }

        $token = $authenticationService->refresh($currentToken);

        return GetRefreshResponse::get($token);
    }

    /**
     * Login route.
     *
     * @param PostLoginArgumentResolver $request               The request.
     * @param AuthenticationService     $authenticationService The authentication service.
     *
     * @Route("/login", methods={"POST"})
     *
     * @return PostLoginResponse
     *
     * @throws ApiException                  When the includes passed are not array values.
     * @throws AuthenticationFailedException When authentication fails.
     */
    public function postLogin(PostLoginArgumentResolver $request, AuthenticationService $authenticationService): PostLoginResponse
    {
        $token = $authenticationService->login($request->getUsername(), $request->getPassword());

        return PostLoginResponse::get($token);
    }

}
