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

use App\Entity\UserEntity;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class UserService.
 */
class UserService
{

    /**
     * The user repository.
     *
     * @var UserRepository $userRepository
     */
    private $userRepository;

    /**
     * The token storage.
     *
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * UserService constructor.
     *
     * @param UserRepository        $userRepository The user repository.
     * @param TokenStorageInterface $tokenStorage   The token storage.
     */
    public function __construct(UserRepository $userRepository, TokenStorageInterface $tokenStorage)
    {
        $this->userRepository = $userRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Get all users.
     *
     * @return array
     */
    public function getAllUsers(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * Get the current profile.
     *
     * @return UserEntity|null
     */
    public function getProfile(): UserEntity
    {
        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return null;
        }

        return $token->getUser();
    }

}
