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
use App\Exception\AuthenticationFailedException;
use App\Exception\DatabaseException;
use App\Exception\InvalidTokenException;
use App\Exception\TokenNotFoundException;
use App\Service\AuthenticationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The LoginController Class.
 */
class LoginController extends BaseController
{

    /**
     * Refresh route.
     *
     * @param Request               $request               The request.
     * @param AuthenticationService $authenticationService The authentication service.
     *
     * @Route("/login/refresh", methods={"POST"})
     *
     * @return JsonResponse
     *
     * @throws TokenNotFoundException When the token was not found in the Authorization header.
     * @throws DatabaseException      When the refresh token could not be saved.
     * @throws InvalidTokenException  When the token appears to be invalid or expired.
     */
    public function postRefresh(Request $request, AuthenticationService $authenticationService): JsonResponse
    {
        $currentToken = str_replace('Bearer ', '', $request->headers->get('Authorization'));
        if($currentToken === null) {
            throw new TokenNotFoundException();
        }

        $token = $authenticationService->refresh($currentToken);

        return $this->view(['message' => 'Refresh successful', 'token' => $token], Response::HTTP_OK);
    }

    /**
     * Login route.
     *
     * @param PostLoginArgumentResolver $request               The request.
     * @param AuthenticationService     $authenticationService The authentication service.
     *
     * @Route("/login", methods={"POST"})
     *
     * @return JsonResponse
     *
     * @throws AuthenticationFailedException When authentication fails.
     */
    public function postLogin(PostLoginArgumentResolver $request, AuthenticationService $authenticationService): JsonResponse
    {
        $token = $authenticationService->login($request->getUsername(), $request->getPassword());

        return $this->view(['message' => 'Login successful', 'token' => $token], Response::HTTP_OK);
    }

}
