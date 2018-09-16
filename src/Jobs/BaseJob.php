<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Jobs;

use App\Interfaces\JobInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BaseJob.
 */
abstract class BaseJob implements JobInterface
{

    /**
     * The container.
     *
     * @var ContainerInterface $container
     */
    public $container;

    /**
     * Always call the handle function when the job is created.
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
     * Handle the job.
     *
     * @return mixed
     */
    abstract public function handle();

}
