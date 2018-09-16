<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Jobs\User;

use App\Jobs\BaseJob;
use App\Repository\UserRepository;

/**
 * Class GetAllUsersJob.
 */
class GetAllUsersJob extends BaseJob
{

    /**
     * Handle the job.
     *
     * @return array
     */
    public function handle(): array
    {
        $repository = new UserRepository($this->container->get('doctrine'));

        return $repository->findAll();
    }

}
