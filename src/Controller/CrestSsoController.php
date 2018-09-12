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

use App\Entity\CharacterEntity;
use App\Exception\CrestSsoApiException;
use App\Exception\InvalidStateException;
use App\Exception\UserNotFoundException;
use App\Repository\CharacterRepository;
use App\Repository\UserRepository;
use App\Service\Eve\CrestSsoService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CrestSsoController.
 */
class CrestSsoController extends FOSRestController implements ClassResourceInterface
{

    /**
     * The CharacterRepository.
     *
     * @var CharacterRepository $characterRepository
     */
    private $characterRepository;

    /**
     * The userRepository.
     *
     * @var UserRepository $userRepository
     */
    private $userRepository;

    /**
     * UserController constructor.
     *
     * @param CharacterRepository $characterRepository The characterRepository.
     * @param UserRepository      $userRepository      The userRepository.
     */
    public function __construct(
        CharacterRepository $characterRepository,
        UserRepository $userRepository
    ){
        $this->characterRepository = $characterRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Redirect to the Eve Sso provider.
     *
     * @param Request $request The original request.
     * @param string  $userId  The id of the user.
     *
     * @Rest\Get("sso/login/{userId}")
     *
     * @return RedirectResponse
     *
     * @throws UserNotFoundException Thrown when the user was not found.
     * @throws CrestSsoApiException  Thrown when the CrestSsoService failed to initialize.
     */
    public function getAction(Request $request, string $userId): RedirectResponse
    {
        $user = $this->userRepository->find($userId);

        if ($user === null) {
            throw new UserNotFoundException();
        }

        $sso = new CrestSsoService();

        $redirect = 'userId=' . $userId;
        $redirect = $request->get('redirect') ? $redirect . '&redirect=' . $request->get('redirect') : $redirect;

        return $this->redirect($sso->getLoginUrl($request->getSession(), $redirect), Response::HTTP_SEE_OTHER);
    }

    /**
     * Get the profile of the current logged in user.
     *
     * @param Request $request The original request.
     *
     * @Rest\Get("sso/callback")
     *
     * @return RedirectResponse
     *
     * @throws InvalidStateException Thrown when the state seems invalid.
     * @throws UserNotFoundException Thrown when the user was not found.
     * @throws CrestSsoApiException  Thrown when the CrestSsoService failed to initialize or the callback fails.
     */
    public function getCallbackAction(Request $request): RedirectResponse
    {
        $user = $this->userRepository->find($request->get('userId'));

        if ($user === null) {
            throw new UserNotFoundException();
        }

        $sso = new CrestSsoService();

        $code = $request->get('code');
        $state = $request->get('state');
        $sessionState = $request->getSession()->get('state');
        $request->getSession()->remove('state');

        $callback = $sso->handleCallback($code, $state, $sessionState);

        $character = $this->characterRepository->findOneBy([
            'characterId' => $callback['characterId'],
        ]);

        if ($character === null) {
            $character = new CharacterEntity();
            $character->setCharacterId($callback['characterId']);
            $character->setCharacterName($callback['characterName']);
            $character->setScopes($callback['scopes']);
            $character->setTokenType($callback['tokenType']);
            $character->setOwnerHash($callback['ownerHash']);
            $character->setRefreshToken($callback['refreshToken']);
            $character->setAccessToken($callback['accessToken']);
            $character->setAvatar('https://image.eveonline.com/Character/' . $callback['characterId'] . '_512.jpg');
            $character->setUserId($user);

            $user->addCharacter($character);
            $this->userRepository->save($user);
        }

        if ($character !== null) {
            $character->setScopes($callback['scopes']);
            $character->setTokenType($callback['tokenType']);
            $character->setOwnerHash($callback['ownerHash']);
            $character->setRefreshToken($callback['refreshToken']);
            $character->setAccessToken($callback['accessToken']);
            $character->setUser($user);
            $this->characterRepository->save($character);
        }

        $redirect = $request->get('redirect') ? '/' . $request->get('redirect') : '';

        return $this->redirect($sso->getFrontUrl() . $redirect, Response::HTTP_SEE_OTHER);
    }

}
