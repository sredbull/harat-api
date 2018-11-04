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

use App\Entity\GroupEntity;
use App\Entity\RefreshTokenEntity;
use App\Entity\UserEntity;
use App\Exception\AuthenticationFailedException;
use App\Exception\DatabaseException;
use App\Exception\InvalidTokenException;
use App\Exception\LdapGroupNotFoundException;
use App\Exception\LdapUserNotFoundException;
use App\Exception\RegistrationFailedException;
use App\Repository\GroupRepository;
use App\Repository\RefreshTokenRepository;
use App\Repository\UserRepository;
use LdapTools\LdapManager;
use LdapTools\Object\LdapObject;
use LdapTools\Operation\AuthenticationOperation;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegistrationController.
 */
class AuthenticationService
{

    public const DEFAULT_GROUP = 'harat-users';
    public const DEFAULT_PEOPLE_OU = 'ou=people,dc=housearatus,dc=space';

    /**
     * The ldap manager.
     *
     * @var LdapManager $ldapManager
     */
    private $ldapManager;

    /**
     * The user repository.
     *
     * @var UserRepository $userRepository
     */
    private $userRepository;

    /**
     * The user repository.
     *
     * @var RefreshTokenRepository $refreshTokenRepository
     */
    private $refreshTokenRepository;

    /**
     * The password encoder.
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * The group repository.
     *
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * The JWT token manager.
     *
     * @var JWTTokenManagerInterface
     */
    private $jwtTokenManager;

    /**
     * RegistrationController constructor.
     *
     * @param GroupRepository              $groupRepository        The group repository.
     * @param JWTTokenManagerInterface     $jwtTokenManager        The JWT token manager.
     * @param LdapManager                  $ldapManager            The LDAP manager.
     * @param UserPasswordEncoderInterface $encoder                The password encoder.
     * @param RefreshTokenRepository       $refreshTokenRepository The refresh token repository.
     * @param UserRepository               $userRepository         The user repository.
     */
    public function __construct(
        GroupRepository $groupRepository,
        JWTTokenManagerInterface $jwtTokenManager,
        LdapManager $ldapManager,
        UserPasswordEncoderInterface $encoder,
        RefreshTokenRepository $refreshTokenRepository,
        UserRepository $userRepository
    )
    {
        $this->encoder = $encoder;
        $this->groupRepository = $groupRepository;
        $this->jwtTokenManager = $jwtTokenManager;
        $this->ldapManager = $ldapManager;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Login with credentials.
     *
     * @param string $username The user to login.
     * @param string $password The password to authenticate with.
     *
     * @return string
     *
     * @throws AuthenticationFailedException When authenticating fails.
     */
    public function login(string $username, string $password): string
    {
        $auth = $this->ldapManager->getConnection()->execute(
            new AuthenticationOperation(
                sprintf('uniqueIdentifier=%s,%s', $username, self::DEFAULT_PEOPLE_OU),
                $password
            )
        );

        if ($auth->isAuthenticated() === false) {
            throw new AuthenticationFailedException();
        }

        try {
            $ldapUser = $this->getLdapUser($username);
            $ldapGroupUsers = $this->getLdapGroupUsers();
            $this->addUserToLdapGroup($ldapUser, $ldapGroupUsers);
            $ldapGroups = $ldapUser->getGroups() ?? [];
            $userGroups = $this->groupRepository->findAllGroupNames();
            $this->syncLdapGroups($ldapGroups, $userGroups);
            $systemUser = $this->setSystemUserGroups($this->getSystemUser($ldapUser, $password), $ldapGroups);
            $systemUser->setLastLogin(new \DateTime('now'));
            $this->userRepository->save($systemUser);
            $token = $this->jwtTokenManager->create($systemUser);
            $this->generateRefreshToken($systemUser, $token);

            return $token;
        } catch (\Throwable $e) {
            throw new AuthenticationFailedException();
        }
    }

    /**
     * Refresh the login.
     *
     * @param string $token The token.
     *
     * @return string
     *
     * @throws InvalidTokenException When the token is invalid or expired.
     * @throws DatabaseException     When the refresh token could not be saved.
     */
    public function refresh(string $token): string
    {
        $currentToken = $this->refreshTokenRepository->findByToken($token);
        if ($currentToken === null) {
            throw new InvalidTokenException();
        }

        if (new \Datetime('now') > $currentToken->getValid() === true) {
            throw new InvalidTokenException('Token is expired');
        }

        $token = $this->jwtTokenManager->create($currentToken->getUser());
        $this->generateRefreshToken($currentToken->getUser(), $token);

        return $token;
    }

    /**
     * Get the encoded password.
     *
     * @param string $username The username.
     * @param string $password The password.
     *
     * @return string
     */
    public function getEncodedPassword(string $username, string $password): string
    {
        $user = new UserEntity();
        $user->setUsername($username);

        return $this->encoder->encodePassword($user, $password);
    }

    /**
     * Get the system user.
     *
     * @param LdapObject $ldapUser The ldap user.
     * @param string     $password The password.
     *
     * @return UserEntity
     */
    public function getSystemUser(LdapObject $ldapUser, string $password): UserEntity
    {
        $user = $this->userRepository->findByUsername($ldapUser->getUsername());
        if ($user === null) {
            $user = new UserEntity();
            $user->setEmail($ldapUser->getEmail());
            $user->setUsername($ldapUser->getUsername());
            $user->setPassword($this->encoder->encodePassword($user, $password));
            $user->setEnabled(true);
        }

        return $user;
    }

    /**
     * Get the ldap user.
     *
     * @param string $username The username.
     *
     * @return LdapObject
     *
     * @throws LdapUserNotFoundException When the user could not be found.
     */
    public function getLdapUser(string $username): LdapObject
    {
        try {
            return $this->ldapManager->buildLdapQuery()
                ->fromUsers()
                ->where(['username' => $username])
                ->getLdapQuery()
                ->getSingleResult();
        } catch (\Throwable $e) {
            throw new LdapUserNotFoundException();
        }
    }

    /**
     * Get users for an ldap group.
     *
     * @param string $group The group.
     *
     * @return LdapObject
     *
     * @throws LdapGroupNotFoundException When the group could not be found.
     */
    public function getLdapGroupUsers(string $group = self::DEFAULT_GROUP): LdapObject
    {
        try {
            return $this->ldapManager->buildLdapQuery()
                ->select('uniqueMember')
                ->where([
                    'cn' => $group,
                    'objectClass' => 'groupOfUniqueNames',
                ])
                ->getLdapQuery()
                ->getSingleResult();
        } catch (\Throwable $e) {
            throw new LdapGroupNotFoundException();
        }
    }

    /**
     * Add an user to a group.
     *
     * @param LdapObject $user  The ldap user.
     * @param LdapObject $group The ldap group.
     *
     * @return void
     */
    public function addUserToLdapGroup(LdapObject $user , LdapObject $group): void
    {
        $userGroup = sprintf('uniqueIdentifier=%s,%s', $user->getUsername(), self::DEFAULT_PEOPLE_OU);
        if ($group->hasUniqueMember($userGroup) === false) {
            $group->addUniqueMember($userGroup);
            $this->ldapManager->persist($group);
        }
    }

    /**
     * Sync the ldap groups with system groups.
     *
     * @param array $ldapGroups The ldap groups.
     * @param array $userGroups The user groups.
     *
     * @throws DatabaseException When saving the groups fails.
     *
     * @return void
     */
    public function syncLdapGroups(array $ldapGroups, array $userGroups): void
    {
        $diffGroups = array_diff($ldapGroups, $userGroups);
        if (count($diffGroups) === 0) {
            return;
        }

        foreach ($diffGroups as $group) {
            $newGroup = new GroupEntity($group);
            $this->groupRepository->persist($newGroup);
        }
        $this->groupRepository->flush();
    }

    /**
     * Set the system user groups.
     *
     * @param UserEntity $user       The system user.
     * @param array      $ldapGroups The ldap groups.
     *
     * @return UserEntity
     */
    public function setSystemUserGroups(UserEntity $user, array $ldapGroups): UserEntity
    {
        $existingGroups = $this->groupRepository->findBy([
            'name' => $ldapGroups,
        ]);

        foreach ($existingGroups as $group) {
            $user->addGroup($group);
        }

        return $user;
    }

    /**
     * Register a new user.
     *
     * @param string $email    The email of the new user.
     * @param string $username The username of the new user.
     * @param string $password The password of the new user.
     *
     * @return void
     *
     * @throws RegistrationFailedException When registering fails.
     */
    public function register(string $email, string $username, string $password): void
    {
        try {
            $this->ldapManager
                ->createLdapObject()
                ->createUser()
                ->in(self::DEFAULT_PEOPLE_OU)
                ->with([
                    'email' => $email,
                    'name' => $username,
                    'password' => $password,
                    'username' => $username,
                    'uid' => $username,
                ])
                ->execute();
        } catch (\Throwable $exception) {
            throw new RegistrationFailedException(sprintf('Registration failed: %s', $exception->getMessage()));
        }
    }

    /**
     * Generate the refresh token for this user.
     *
     * @param UserEntity $systemUser The user.
     * @param string     $token      The token.
     *
     * @throws DatabaseException When storing the refresh token fails.
     *
     * @return RefreshTokenEntity
     */
    public function generateRefreshToken(UserEntity $systemUser, string $token): RefreshTokenEntity
    {
        $refreshToken = $systemUser->getRefreshToken();
        if ($refreshToken === null) {
            $refreshToken = new RefreshTokenEntity();
        }

        $refreshToken->setUser($systemUser);
        $refreshToken->setRefreshToken($token);
        $refreshToken->setValid(new \Datetime('+1 months'));

        $this->refreshTokenRepository->save($refreshToken);

        return $refreshToken;
    }

}
