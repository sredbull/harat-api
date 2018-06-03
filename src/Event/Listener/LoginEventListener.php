<?php
namespace App\Event\Listener;

use App\Entity\Group;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use LdapTools\Bundle\LdapToolsBundle\Event\AuthenticationHandlerEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class LoginEventListener.
 *
 * @package App\Event\Listener
 */
class LoginEventListener
{
    /**
     * The Doctrine entity manager.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * The password encoder manager.
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * The groupRepository.
     *
     * @var GroupRepository
     */
    private $groupRepository;

    /**
     * The JWT token manager.
     *
     * @var JWTTokenManagerInterface
     */
    private $JWTTokenManager;

    /**
     * The userRepository.
     *
     * @var UserRepository
     */
    private $userRepository;

    /**
     * LoginEventListener constructor.
     *
     * @param EntityManagerInterface       $entityManager   The Doctrine entity manager.
     * @param GroupRepository              $groupRepository The GroupRepository.
     * @param JWTTokenManagerInterface     $JWTTokenManager The JWT token manager.
     * @param UserPasswordEncoderInterface $encoder         The password encoder manager.
     * @param UserRepository               $userRepository  The UserRepository.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        GroupRepository $groupRepository,
        JWTTokenManagerInterface $JWTTokenManager,
        UserPasswordEncoderInterface $encoder,
        UserRepository $userRepository
    ){
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        $this->JWTTokenManager = $JWTTokenManager;
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
    }


    /**
     * Handle the login success.
     *
     * @param AuthenticationHandlerEvent $event The event with the authentication data.
     *
     * @return AuthenticationHandlerEvent
     */
    public function onLoginSuccess(AuthenticationHandlerEvent $event) : AuthenticationHandlerEvent
    {

        $ldapUser = $event->getToken()->getUser();
        $user = $this->userRepository->findOneBy([
            'username' => $ldapUser->get('username')
        ]);

        if ($user === null) {
            $user = new User();
            $user->setEmail($ldapUser->get('mail'));
            $user->setUsername($ldapUser->get('username'));
            $user->setPassword($this->encoder->encodePassword($user, $event->getRequest()->get('password')));
            $user->setEnabled(true);
        }

        $ldapGroups = $ldapUser->get('groups');
        $userGroups = $this->groupRepository->findAllGroupNames();
        $diffGroups = array_diff($ldapGroups, $userGroups);

        if (count($diffGroups) > 0) {
            foreach ($diffGroups as $group) {
                $newGroup = new Group($group);
                $this->entityManager->persist($newGroup);
            }
            $this->entityManager->flush();
        }

        foreach ($ldapGroups as $group) {
            $existingGroup = $this->groupRepository->findOneBy([
                'name' => $group
            ]);
            $user->addGroup($existingGroup);
        }

        $token = $this->JWTTokenManager->create($user);
        $user->setLastLogin(new \DateTime('now'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $response = new Response(
            json_encode(
                [
                    'token' => $token
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
    public function onLoginFailure(AuthenticationHandlerEvent $event) : AuthenticationHandlerEvent
    {
        $response = new Response(
            json_encode(
                [
                    'status' => 'error',
                    'message' => $event->getException()->getMessage()
                ]
            ),
            Response::HTTP_UNAUTHORIZED
        );

        $event->setResponse($response);
        return $event;
    }
}
