<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Jobs\Recruitment;

use App\Entity\UserEntity;
use App\Exception\DatabaseException;
use App\Jobs\BaseJob;
use App\Repository\RecruitmentRepository;

/**
 * Class DeleteRecruitmentsFromUserJob.
 */
class DeleteRecruitmentsFromUserJob extends BaseJob
{

    /**
     * The user to delete the recruitments from.
     *
     * @var UserEntity
     */
    private $user;

    /**
     * DeleteRecruitmentsFromUserJob constructor.
     *
     * @param UserEntity $user The user.
     */
    public function __construct(UserEntity $user)
    {
        $this->user = $user;
    }

    /**
     * Handle the job.
     *
     * @return void
     *
     * @throws DatabaseException When removing the entity fails.
     */
    public function handle(): void
    {
        $repository = new RecruitmentRepository($this->container->get('doctrine'));

        foreach($this->user->getRecruitments() as $recruitment) {
            $repository->remove($recruitment);
        }
    }

}
