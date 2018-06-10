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

use Symfony\Component\Validator\Constraint;

/**
 * ExistingLdapUser Class.
 *
 * @Annotation
 */
class ExistingLdapUser extends Constraint
{

    /**
     * The error message.
     *
     * @var string $message
     */
    public $message = 'The user "{{ string }}" already exists.';

    /**
     * Returns the the validation class to be used.
     *
     * @return string
     */
    public function validatedBy(): string
    {
        return get_class($this).'Validator';
    }
}