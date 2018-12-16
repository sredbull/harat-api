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
use App\Exception\ApiException;
use App\Exception\RegistrationFailedException;
use App\Response\Registration\PostRegistrationResponse;
use App\Service\AuthenticationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistrationController.
 */
class RegistrationController extends AbstractController
{

    /**
     * Register a new user.
     *
     * @param PostRegisterArgumentResolver $request               The request.
     * @param AuthenticationService        $authenticationService The authentication service.
     *
     * @Route("/register", methods={"POST"})
     *
     * @return PostRegistrationResponse
     *
     * @throws RegistrationFailedException When registration fails.
     */
    public function postRegistration(PostRegisterArgumentResolver $request, AuthenticationService $authenticationService): PostRegistrationResponse
    {
        $authenticationService->register(
            $request->getEmail(),
            $request->getUsername(),
            $request->getPassword()
        );

        return PostRegistrationResponse::get();
    }

}

