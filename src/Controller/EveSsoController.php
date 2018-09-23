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

use App\Entity\UserEntity;
use App\Exception\DatabaseException;
use App\Exception\EveSsoException;
use App\Exception\InvalidStateException;
use App\Exception\UserNotFoundException;
use App\ParamConverter\EveSso\GetCallbackRequest;
use App\ParamConverter\EveSso\GetRedirectRequest;
use App\Service\EveSsoService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EveSsoController.
 */
class EveSsoController extends BaseController
{

    /**
     * Redirect to the Eve Sso provider.
     *
     * @param EveSsoService      $eveSsoService The Eve Sso service.
     * @param UserEntity|null    $user          The user.
     * @param GetRedirectRequest $request       The request.
     *
     * @return RedirectResponse
     *
     * @Route("/sso/login/{id}", methods={"GET"})
     *
     * @throws UserNotFoundException When the user was not found.
     */
    public function getRedirect(EveSsoService $eveSsoService, ?UserEntity $user, GetRedirectRequest $request): RedirectResponse
    {
        if ($user === null) {
            throw new UserNotFoundException();
        }

        return $this->redirect($eveSsoService->getLoginUrl($user->getId(), $request->getRedirect()), Response::HTTP_SEE_OTHER);
    }

    /**
     * Get the profile of the current logged in user.
     *
     * @param EveSsoService      $eveSsoService The Eve Sso service.
     * @param GetCallbackRequest $request       The request.
     *
     * @Route("/sso/callback", methods={"GET"})
     *
     * @return RedirectResponse
     *
     * @throws DatabaseException     When setting the character fails.
     * @throws EveSsoException       When when the CrestSsoService failed to initialize or the callback fails.
     * @throws InvalidStateException When the state seems invalid.
     * @throws UserNotFoundException When the user could not be found.
     */
    public function getCallback(EveSsoService $eveSsoService, GetCallbackRequest $request): RedirectResponse
    {
        $characterData = $eveSsoService->handleCallback($request->getCode(), $request->getState());
        $eveSsoService->setCharacterForUser($request->getUserId(), $characterData);

        $url = $eveSsoService->getFrontUrl();
        if ($request->getRedirect() !== null) {
            $url = $eveSsoService->getFrontUrl() . $request->getRedirect();
        }

        return $this->redirect($url, Response::HTTP_SEE_OTHER);
    }

}
