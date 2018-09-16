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

use App\Entity\RecruitmentEntity;
use App\Entity\UserEntity;
use App\Exception\DatabaseException;
use App\Jobs\BaseJob;
use App\Repository\RecruitmentRepository;

/**
 * Class NewRecruitmentForUserJob.
 */
class NewRecruitmentForUserJob extends BaseJob
{

    /**
     * The user to create the new recruitments for.
     *
     * @var UserEntity
     */
    private $user;

    /**
     * The recruitment form.
     *
     * @var array
     */
    private $form;

    /**
     * DeleteRecruitmentsFromUserJob constructor.
     *
     * @param UserEntity $user The user.
     * @param array      $form The recruitment form.
     */
    public function __construct(UserEntity $user, array $form)
    {
        $this->user = $user;
        $this->form = $form;
    }

    /**
     * Handle the job.
     *
     * @return void
     *
     * @throws DatabaseException When saving the entity fails.
     */
    public function handle(): void
    {
        $repository = new RecruitmentRepository($this->container->get('doctrine'));
        $recruitment = new RecruitmentEntity();
        $recruitment->setUser($this->user);
        $recruitment->setForm($this->form);
        $repository->save($recruitment);
    }

}
