<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright House Aratus
 */
namespace App\Event\Listener;

use App\Entity\GroupEntity;
use App\Entity\UserEntity;
use App\Exception\DatabaseException;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use LdapTools\Bundle\LdapToolsBundle\Event\AuthenticationHandlerEvent;
use LdapTools\Exception\EmptyResultException;
use LdapTools\Exception\MultiResultException;
use LdapTools\LdapManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class LoginEventListener.
 */
class LoginEventListener
{

    /**
     * The Doctrine entity manager.
     *
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * The password encoder manager.
     *
     * @var UserPasswordEncoderInterface $encoder
     */
    private $encoder;

    /**
     * The groupRepository.
     *
     * @var GroupRepository $groupRepository
     */
    private $groupRepository;

    /**
     * The JWT token manager.
     *
     * @var JWTTokenManagerInterface $jwtTokenManager
     */
    private $jwtTokenManager;

    /**
     * The ldap manager.
     *
     * @var LdapManager $ldapManager
     */
    private $ldapManager;

    /**
     * The userRepository.
     *
     * @var UserRepository $userRepository
     */
    private $userRepository;

    /**
     * LoginEventListener constructor.
     *
     * @param EntityManagerInterface       $entityManager   The Doctrine entity manager.
     * @param GroupRepository              $groupRepository The GroupRepository.
     * @param JWTTokenManagerInterface     $jwtTokenManager The JWT token manager.
     * @param LdapManager                  $ldapManager     The LDAP manager.
     * @param UserPasswordEncoderInterface $encoder         The password encoder manager.
     * @param UserRepository               $userRepository  The UserRepository.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        GroupRepository $groupRepository,
        JWTTokenManagerInterface $jwtTokenManager,
        LdapManager $ldapManager,
        UserPasswordEncoderInterface $encoder,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        $this->jwtTokenManager = $jwtTokenManager;
        $this->ldapManager = $ldapManager;
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
    }


    /**
     * Handle the login success.
     *
     * @param AuthenticationHandlerEvent $event The event with the authentication data.
     *
     * @return AuthenticationHandlerEvent
     *
     * @throws EmptyResultException When the result seems to be empty.
     * @throws MultiResultException When the result seems to have more than one result.
     * @throws DatabaseException    When Something fails with saving or deleting from the database.
     */
    public function onLoginSuccess(AuthenticationHandlerEvent $event): AuthenticationHandlerEvent
    {

        $ldapUser = $event->getToken()->getUser();
        $user = $this->userRepository->findOneBy([
            'username' => $ldapUser->getUsername(),
        ]);

        $group = $this->ldapManager->buildLdapQuery()
            ->select('uniqueMember')
            ->where([
                'cn' => 'harat-users',
                'objectClass' => 'groupOfUniqueNames',
            ])
            ->getLdapQuery()
            ->getSingleResult();

        $newGroup = 'uniqueIdentifier=' . $ldapUser->getUsername() . ',ou=people,dc=housearatus,dc=space';

        if (\in_array($newGroup, $group->getUniqueMember(), true) === false) {
            $group->addUniqueMember($newGroup);
            $this->ldapManager->persist($group);
        }

        if ($user === null) {
            $user = new UserEntity();
            $user->setEmail($ldapUser->getMail());
            $user->setUsername($ldapUser->getUsername());
            $user->setPassword($this->encoder->encodePassword($user, $event->getRequest()->get('password')));
            $user->setEnabled(true);
        }

        $ldapGroups = $ldapUser->getGroups() ?? [];
        $userGroups = $this->groupRepository->findAllGroupNames();
        $diffGroups = array_diff($ldapGroups, $userGroups);

        if (count($diffGroups) > 0) {
            foreach ($diffGroups as $group) {
                $newGroup = new GroupEntity($group);
                $this->entityManager->persist($newGroup);
            }
            $this->entityManager->flush();
        }

        foreach ($ldapGroups as $group) {
            $existingGroup = $this->groupRepository->findOneBy([
                'name' => $group,
            ]);
            $user->addGroup($existingGroup);
        }

        $token = $this->jwtTokenManager->create($user);
        $user->setLastLogin(new \DateTime('now'));

        $this->userRepository->save($user);

        $response = new Response(
            json_encode(
                [
                    'code' => Response::HTTP_OK,
                    'status' => 'ok',
                    'message' => 'Login successful',
                    'token' => $token,
                ]
            ),
            Response::HTTP_OK
        );

        $event->setResponse($response);

        return $event;
    }


    /**
     * Handle the login failure.
     *
     * @param AuthenticationHandlerEvent $event The event with the authentication data.
     *
     * @return AuthenticationHandlerEvent
     */
    public function onLoginFailure(AuthenticationHandlerEvent $event): AuthenticationHandlerEvent
    {
        $response = new Response(
            json_encode(
                [
                    'status' => 'error',
                    'message' => $event->getException()->getMessage(),
                ]
            ),
            Response::HTTP_UNAUTHORIZED
        );

        $event->setResponse($response);

        return $event;
    }

}
