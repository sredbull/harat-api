<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Features;

use App\Interfaces\FeatureInterface;
use App\Interfaces\JobInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BaseFeature.
 */
abstract class BaseFeature implements FeatureInterface
{

    /**
     * The container.
     *
     * @var ContainerInterface $container
     */
    public $container;

    /**
     * Always call the handle function when the feature is created.
     *
     * @param ContainerInterface $container The container.
     *
     * @return mixed
     */
    public function __invoke(ContainerInterface $container)
    {
        $this->container = $container;

        return $this->handle();
    }

    /**
     * Handle the feature.
     *
     * @return mixed
     */
    abstract public function handle();

    /**
     * Run the job.
     *
     * @param JobInterface $job The job.
     *
     * @return mixed
     */
    public function run(JobInterface $job)
    {
        return $job($this->container);
    }

}
