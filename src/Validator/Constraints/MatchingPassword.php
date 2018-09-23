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
 * MatchingPassword Class.
 */
class MatchingPassword extends Constraint
{

    /**
     * The error message.
     *
     * @var string $message
     */
    public $message = 'The passwords do not match';

    /**
     * The property path
     *
     * @var string $propertyPath
     */
    public $propertyPath;

    /**
     * MatchingPassword constructor.
     *
     * @param array|null $options The options.
     *
     * @throws \InvalidArgumentException When the type parameter does not contain a valid value.
     */
    public function __construct(?array $options = null)
    {
        if (
            \is_array($options) === false ||
            array_key_exists('propertyPath', $options) === false ||
            \is_string($options['propertyPath']) === false
        ) {
            throw new \InvalidArgumentException('The "propertyPath" parameter is invalid or missing.');
        }

        parent::__construct($options);
    }

}
