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
namespace App\Annotation;

/**
 * @Annotation
 *
 * @Target("PROPERTY")
 */
final class Linked
{
    /**
     * The accessor getter.
     *
     * @var string $accessor
     *
     * @Required
     */
    public $accessor;

    /**
     * The identifier getter.
     *
     * @var string $identifier
     */
    public $identifier = 'getId';

    /**
     * The href.
     *
     * @var string $href
     *
     * @Required
     */
    public $href;

}
