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
 */
class ExistingLdapUser extends Constraint
{

    public const TYPE_EMAIL = 'email';
    public const TYPE_USERNAME = 'username';

    /**
     * The error message.
     *
     * @var string $message
     */
    public $message;

    /**
     * The validation type.
     *
     * @var string $type
     */
    public $type;

    /**
     * The allowed validation types.
     *
     * @var array $allowedTypes
     */
    public static $allowedTypes = [
        self::TYPE_EMAIL,
        self::TYPE_USERNAME,
    ];

    /**
     * ExistingLdapUser constructor.
     *
     * @param array|null $options The options.
     *
     * @throws \InvalidArgumentException When the type parameter does not contain a valid value.
     */
    public function __construct(?array $options = null)
    {
        if (
            \is_array($options) === false ||
            array_key_exists('type', $options) === false ||
            \in_array($options['type'], self::$allowedTypes, true) === false

        ) {
            throw new \InvalidArgumentException('The "type" parameter value is not valid.');
        }

        $this->message = sprintf('The %s {{ string }} already exists.', $options['type']);

        parent::__construct($options);
    }

}
