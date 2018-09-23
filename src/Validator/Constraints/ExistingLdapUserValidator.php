<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Validator\Constraints;

use LdapTools\Exception\EmptyResultException;
use LdapTools\Exception\MultiResultException;
use LdapTools\LdapManager;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ExistingLdapUserValidator.
 */
class ExistingLdapUserValidator extends ConstraintValidator
{

    /**
     * The ldap manager.
     *
     * @var LdapManager $ldapManager
     */
    private $ldapManager;

    /**
     * ExistingLdapUserValidator constructor.
     *
     * @param LdapManager $ldapManager The ldap manager.
     */
    public function __construct(LdapManager $ldapManager)
    {
        $this->ldapManager = $ldapManager;
    }

    /**
     * The validation.
     *
     * @param mixed      $value      The value to validate against.
     * @param Constraint $constraint The validation constraint.
     *
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        try {
            $this->ldapManager->buildLdapQuery()->fromUsers()
                ->select([$constraint->type])
                ->where([$constraint->type => $value])
                ->getLdapQuery()
                ->getSingleResult();
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        } catch (EmptyResultException $exception) {
            // Just catch the exception.
        } catch (MultiResultException $exception) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }

    }

}
