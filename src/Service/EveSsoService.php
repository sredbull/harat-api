<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Service;

use App\Entity\CharacterEntity;
use App\Exception\DatabaseException;
use App\Exception\EveSsoException;
use App\Exception\InvalidStateException;
use App\Exception\UserNotFoundException;
use App\Repository\CharacterRepository;
use App\Repository\UserRepository;
use GuzzleHttp\Client;
use Psr\Http\Message\MessageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class CrestSso.
 */
class EveSsoService
{

    /**
     * The id of the client.
     *
     * @var string $clientID
     */
    private $clientId;

    /**
     * The secret key.
     *
     * @var string $secretKey
     */
    private $secretKey;

    /**
     * The callback url.
     *
     * @var string $callbackURL
     */
    private $callbackUrl;

    /**
     * The security definition scopes.
     *
     * @var array|null $scopes
     */
    private $scopes;

    /**
     * The session state.
     *
     * @var string $state
     */
    private $state;

    /**
     * The login url.
     *
     * @var string $loginUrl
     */
    private $loginUrl = 'https://login.eveonline.com/oauth/authorize';

    /**
     * The token url.
     *
     * @var string $tokenUrl
     */
    private $tokenUrl = 'https://login.eveonline.com/oauth/token';

    /**
     * The verify url.
     *
     * @var string $verifyUrl
     */
    private $verifyUrl = 'https://login.eveonline.com/oauth/verify';

    /**
     * The front url.
     *
     * @var string $frontUrl
     */
    private $frontUrl;

    /**
     * The session.
     *
     * @var SessionInterface $session
     */
    private $session;

    /**
     * The character repository.
     *
     * @var CharacterRepository $characterRepository
     */

    private $characterRepository;

    /**
     * The user repository.
     *
     * @var UserRepository $userRepository
     */
    private $userRepository;

    /**
     * CrestSSO constructor.
     *
     * @param SessionInterface    $session             The session.
     * @param CharacterRepository $characterRepository The character repository.
     * @param UserRepository      $userRepository      The user repository.
     * @param array|null          $scopes              The scopes.
     *
     * @throws EveSsoException Thrown when the scopes could not be set on initialize.
     */
    public function __construct(
        SessionInterface $session,
        CharacterRepository $characterRepository,
        UserRepository $userRepository,
        ?array $scopes = null
    ) {
        $this->session = $session;
        $this->characterRepository = $characterRepository;
        $this->userRepository = $userRepository;
        $this->scopes = $scopes;

        if ($this->scopes === null) {
            $this->getScopes();
        }

        if (getenv('APP_ENV') === 'dev') {
            $this->clientId = 'f071a6b9fa704850aae4bff6b1b06ce9';
            $this->secretKey = 'ZNW0waxeLIeHjyQQrVg99M1z0ciYSblXi21Tn0f6';
            $this->callbackUrl = 'http://api.housearatus.local:8000/sso/callback';
            $this->frontUrl = 'http://www.housearatus.local:4000/';
        }

        if (getenv('APP_ENV') === 'prod') {
            $this->clientId = '31c2e99186fe4f6db13c7d670ee1fd02';
            $this->secretKey = 'eJkWuADRSJhYmUSfYydKTG1N8qQdGPmxbgPj2CXT';
            $this->callbackUrl = 'https://api.housearatus.space/sso/callback';
            $this->frontUrl = 'https://www.housearatus.space/';
        }
    }

    /**
     * Get the state.
     *
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * Set the state.
     *
     * @param string $state The state.
     *
     * @return void
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * Get the front url.
     *
     * @return string
     */
    public function getFrontUrl(): string
    {
        return $this->frontUrl;
    }

    /**
     * Get the login url.
     *
     * @param integer     $userId   The user id.
     * @param string|null $redirect The params sent with the redirect.
     *
     * @return string
     */
    public function getLoginUrl(int $userId, ?string $redirect = null): string
    {
        if ($this->session->isStarted() === false ) {
            $this->session->start();
        }
        $this->setState($this->session->getId());
        $this->session->set('state', $this->getState());

        $redirectParams = [
            'userId' => $userId,
            'redirect' => $redirect,
        ];

        $fields = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->callbackUrl . '?' . http_build_query($redirectParams),
            'scope' => implode(' ', $this->scopes),
            'state' => $this->getState(),
        ];

        return $this->loginUrl . '?' . http_build_query($fields);
    }

    /**
     * Handle the callback after logging into the sso provider.
     *
     * @param string $code  The code returned by the sso.
     * @param string $state The state returned by the sso.
     *
     * @return array
     *
     * @throws InvalidStateException Thrown when the state seems to be invalid.
     * @throws EveSsoException  Thrown when the callback for some reason fails.
     */
    public function handleCallback(string $code, string $state): array
    {
        $this->validateState($state, $this->session->get('state'));
        $fields = ['grant_type' => 'authorization_code', 'code' => $code];
        $tokenData = $this->doRequest($this->tokenUrl, $fields, null, 'post');
        $accessToken = $tokenData->access_token;
        $refreshToken = $tokenData->refresh_token;
        $verifyData = $this->doRequest($this->verifyUrl, [], $accessToken, 'get');

        return [
            'characterId' => $verifyData->CharacterID,
            'characterName' => $verifyData->CharacterName,
            'scopes' => explode(' ', $verifyData->Scopes),
            'tokenType' => $verifyData->TokenType,
            'ownerHash' => $verifyData->CharacterOwnerHash,
            'refreshToken' => $refreshToken,
            'accessToken' => $accessToken,
        ];
    }

    /**
     * Send a request to the EVE Online sso provider.
     *
     * @param string      $url         The url to query.
     * @param array       $fields      The url fields.
     * @param string|null $accessToken The access token.
     * @param string      $callType    The call type.
     *
     * @return mixed
     *
     * @throws EveSsoException Thrown when the request failed.
     */
    public function doRequest(string $url, array $fields, ?string $accessToken = null, string $callType = 'get')
    {
        try {
            $client = new Client();

            $callType = strtolower($callType);

            /** @var MessageInterface $response */
            $response = $client->$callType($url, [
                'form_params' => $fields,
                'headers' => [
                    'Authorization' => $accessToken !== null ? 'Bearer ' . $accessToken : 'Basic ' . base64_encode($this->clientId . ':' . $this->secretKey),
                ],
                'connect_timeout' => 2,
            ]);

            return json_decode($response->getBody()->getContents());
        } catch (\Throwable $exception) {
            throw new EveSsoException(sprintf('Failed accessing %s', $url));
        }
    }
//    @todo get token from refresh token.
//    public function getAccessToken($refreshToken)
//    {
//        $fields = ['grant_type' => 'refresh_token', 'refresh_token' => $refreshToken];
//        $accessString = $this->doCall($this->tokenURL, $fields, null, 'post');
//        $accessJson = json_decode($accessString, true);
//
//        if (!isset($accessJson['access_token'])) {
//            throw new \Exception("Unexpected value returned from call:\n" . print_r($accessJson, true));
//        }
//
//        return $accessJson['access_token'];
//    }

    /**
     * Set a character for a user.
     *
     * @param string $userId        The user id.
     * @param array  $characterData The character data.
     *
     * @throws DatabaseException     When setting the character fails.
     * @throws UserNotFoundException When the user could not be found.
     *
     * @return void
     */
    public function setCharacterForUser(string $userId, array $characterData): void
    {
        $user = $this->userRepository->find($userId);
        if ($user === null) {
            throw new UserNotFoundException();
        }

        $character = $this->characterRepository->findOneByCharacterId($characterData['characterId']);

        if ($character === null) {
            $character = new CharacterEntity();
            $character->setCharacterId($characterData['characterId']);
            $character->setCharacterName($characterData['characterName']);
            $character->setScopes($characterData['scopes']);
            $character->setTokenType($characterData['tokenType']);
            $character->setOwnerHash($characterData['ownerHash']);
            $character->setRefreshToken($characterData['refreshToken']);
            $character->setAccessToken($characterData['accessToken']);
            $character->setAvatar('https://image.eveonline.com/Character/' . $characterData['characterId'] . '_512.jpg');
            $character->setUser($user);

            $user->addCharacter($character);
            $this->userRepository->save($user);
        }

        if ($character !== null) {
            $character->setScopes($characterData['scopes']);
            $character->setTokenType($characterData['tokenType']);
            $character->setOwnerHash($characterData['ownerHash']);
            $character->setRefreshToken($characterData['refreshToken']);
            $character->setAccessToken($characterData['accessToken']);
            $character->setUser($user);
            $this->characterRepository->save($character);
        }
    }

    /**
     * Get the security definition scopes.
     *
     * @return array|null
     *
     * @throws EveSsoException Thrown when fetching the security definition scopes fails.
     */
    private function getScopes(): ?array
    {
        try {
            $client = new Client();

            $response = $client->get('https://esi.evetech.net/latest/swagger.json', [
                'connect_timeout' => 2,
            ]);

            $responseData = json_decode($response->getBody()->getContents());
            $this->setScopes(\array_keys(\get_object_vars($responseData->securityDefinitions->evesso->scopes)));

            return $this->scopes;
        } catch (\Throwable $exception) {
            throw new EveSsoException('Failed fetching the security definition scopes from: https://esi.evetech.net/latest/swagger.json');
        }
    }

    /**
     * Set the security definition scopes
     *
     * @param array|null $scopes The scopes.
     *
     * @return void
     */
    private function setScopes(?array $scopes): void
    {
        $this->scopes = $scopes;
    }

    /**
     * Validates the state.
     *
     * @param string $state        The current state.
     * @param string $sessionState The session state.
     *
     * @throws InvalidStateException Thrown when the state seems invalid.
     *
     * @return void
     */
    private function validateState(string $state, ?string $sessionState): void
    {
        $this->session->remove('state');
        if ($state !== $sessionState) {
            throw new InvalidStateException();
        }
    }

}
