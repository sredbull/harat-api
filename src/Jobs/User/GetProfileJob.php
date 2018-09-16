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

use App\Entity\UserEntity;
use App\Jobs\BaseJob;

/**
 * Class GetProfileJob.
 */
class GetProfileJob extends BaseJob
{

    /**
     * Handle the job.
     *
     * @return UserEntity|null
     */
    public function handle(): ?UserEntity
    {
        $token = $this->container->get('security.token_storage')->getToken();
        if ($token === null) {
            return null;
        }

        return $token->getUser();
    }

}
