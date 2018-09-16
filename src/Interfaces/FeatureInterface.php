<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Interfaces;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Interface FeatureInterface.
 */
interface FeatureInterface
{

    /**
     * Always call the handle function when the feature is created.
     *
     * @param ContainerInterface $container The container.
     *
     * @return mixed
     */
    public function __invoke(ContainerInterface $container);

    /**
     * Handle the feature.
     *
     * @return mixed
     */
    public function handle();

}
