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

use App\Exception\AuthenticationFailedException;
use App\ParamConverter\Login\PostLoginRequest;
use App\Service\AuthenticationService;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param PostLoginRequest      $request               The request.
     * @param AuthenticationService $authenticationService The authentication service.
     *
     * @Route("/login", methods={"POST"})
     *
     * @return JsonResponse
     *
     * @throws AuthenticationFailedException When authentication fails.
     */
    public function postLogin(PostLoginRequest $request, AuthenticationService $authenticationService): JsonResponse
    {
        $token = $authenticationService->login($request->getUsername(), $request->getPassword());

        return $this->view(['message' => 'Login successful', 'token' => $token], Response::HTTP_OK);
    }

}
