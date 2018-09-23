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

use App\Exception\RegistrationFailedException;
use LdapTools\LdapManager;
use LdapTools\Operation\AuthenticationOperation;

/**
 * Class RegistrationController.
 */
class AuthenticationService
{

    /**
     * The ldap manager.
     *
     * @var LdapManager $ldapManager
     */
    private $ldapManager;

    /**
     * RegistrationController constructor.
     *
     * @param LdapManager $ldapManager The LDAP manager.
     */
    public function __construct(
        LdapManager $ldapManager
    )
    {
        $this->ldapManager = $ldapManager;
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
                ->in('ou=people,dc=housearatus,dc=space')
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

}
