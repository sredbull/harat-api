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

use App\ArgumentResolver\Registration\PostRegisterArgumentResolver;
use App\Exception\RegistrationFailedException;
use App\Service\AuthenticationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistrationController.
 */
class RegistrationController extends BaseController
{

    /**
     * Register a new user.
     *
     * @param AuthenticationService        $authenticationService The authentication service.
     * @param PostRegisterArgumentResolver $request               The request.
     *
     * @Route("/register", methods={"POST"})
     *
     * @return JsonResponse
     *
     * @throws RegistrationFailedException When registration fails.
     */
    public function postRegistration(AuthenticationService $authenticationService, PostRegisterArgumentResolver $request): JsonResponse
    {
        $authenticationService->register(
            $request->getEmail(),
            $request->getUsername(),
            $request->getPassword()
        );

        return $this->view(['message' => 'User registered'], Response::HTTP_OK);
    }

}

