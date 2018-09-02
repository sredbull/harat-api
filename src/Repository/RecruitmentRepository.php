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
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RecruitmentEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecruitmentEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecruitmentEntity[]    findAll()
 * @method RecruitmentEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecruitmentRepository extends ServiceEntityRepository
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

    /**
     * Save changes to the database.
     *
     * @param RecruitmentEntity $entity Entity to persist.
     *
     * @return void
     *
     * @throws ORMException            Thrown when something fails saving the entity.
     * @throws OptimisticLockException Thrown when a version check on an object that uses optimistic locking through a version field fails.
     */
    public function save(RecruitmentEntity $entity): void
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }

}
