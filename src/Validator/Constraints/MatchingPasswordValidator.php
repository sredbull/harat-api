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

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ExistingLdapUserValidator.
 */
class MatchingPasswordValidator extends ConstraintValidator
{

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
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        if ($value === $propertyAccessor->getValue($this->context->getRoot(), $constraint->propertyPath)) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }

}
