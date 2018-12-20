<?php declare (strict_types=1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Provider;

use App\Entity\UserEntity;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 */
class UserProvider implements UserProviderInterface
{

    /**
     * The user repository.
     *
     * @var UserRepository $userRepository
     */
    private $userRepository;

    /**
     * UserProvider constructor.
     *
     * @param UserRepository $userRepository The user repository.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username.
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException If the user is not found.
     */
    public function loadUserByUsername($username): UserInterface
    {
        $user = $this->userRepository->findByUsername($username);

        if ($user === null) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    /**
     * Refreshes the user but completely ignored in a stateless api.
     *
     * @param UserInterface $user The user.
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class The class name.
     *
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return $class === UserEntity::class;
    }
}
