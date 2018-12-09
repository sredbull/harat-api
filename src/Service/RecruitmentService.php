<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Service;

use App\Entity\RecruitmentEntity;
use App\Entity\UserEntity;
use App\Exception\DatabaseException;
use App\Repository\RecruitmentRepository;

class RecruitmentService
{

    /**
     * The recruitment repository.
     *
     * @var RecruitmentRepository $recruitmentRepository
     */
    private $recruitmentRepository;

    /**
     * RecruitmentService constructor.
     *
     * @param RecruitmentRepository $recruitmentRepository The recruitment repository.
     */
    public function __construct(RecruitmentRepository $recruitmentRepository)
    {
        $this->recruitmentRepository = $recruitmentRepository;
    }

    /**
     * Get all recruitments.
     *
     * @return array
     */
    public function getAllRecruitments(): array
    {
        return $this->recruitmentRepository->findAll();
    }

    /**
     * Delete all recruitments for this user.
     *
     * @param UserEntity $user The user.
     *
     * @throws DatabaseException When the recruitment could not be removed.
     *
     * @return void
     */
    public function deleteRecruitmentsFromUser(UserEntity $user): void
    {
        foreach($user->getRecruitments() as $recruitment) {
            $this->recruitmentRepository->remove($recruitment);
        }
    }

    /**
     * Add a new recruitment for this user
     *
     * @param UserEntity $user The user.
     * @param array      $form The recruitment form.
     *
     * @throws DatabaseException When saving the entity fails.
     *
     * @return void
     */
    public function newRecruitmentForUser(UserEntity $user, array $form): void
    {
        $recruitment = new RecruitmentEntity();
        $recruitment->setUser($user);
        $recruitment->setForm($form);
        $this->recruitmentRepository->save($recruitment);
    }

}
