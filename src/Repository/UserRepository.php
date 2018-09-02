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

use App\Entity\UserEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class UserRepository.
 *
 * @method UserEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserEntity[]    findAll()
 * @method UserEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{

    /**
     * UserRepository constructor.
     *
     * @param RegistryInterface $registry The registry interface.
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserEntity::class);
    }

    /**
     * Save changes to the database.
     *
     * @param UserEntity $entity Entity to persist.
     *
     * @return void
     *
     * @throws ORMException            Thrown when something fails saving the entity.
     * @throws OptimisticLockException Thrown when a version check on an object that uses optimistic locking through a version field fails.
     */
    public function save(UserEntity $entity): void
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }

}
