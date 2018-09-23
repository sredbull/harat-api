<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Repository;

use App\Entity\RecruitmentEntity;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class RecruitmentRepository.
 *
 * @method RecruitmentEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecruitmentEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecruitmentEntity[]    findAll()
 * @method RecruitmentEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecruitmentRepository extends BaseRepository
{

    /**
     * RecruitmentRepository constructor.
     *
     * @param RegistryInterface $registry The registry interface.
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RecruitmentEntity::class);
    }

}
