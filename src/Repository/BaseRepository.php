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

use App\Exception\DatabaseException;
use App\Interfaces\EntityInterface;
use App\Interfaces\RepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class BaseRepository.
 */
class BaseRepository extends ServiceEntityRepository implements RepositoryInterface
{

    /**
     * RecruitmentRepository constructor.
     *
     * @param RegistryInterface $registry    The registry interface.
     * @param string            $entityClass The class name of the entity this repository manages.
     */
    public function __construct(RegistryInterface $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * Delete changes from the database.
     *
     * @param EntityInterface $entity Entity to delete.
     *
     * @return void
     *
     * @throws DatabaseException When removing the entity fails.
     */
    public function remove(EntityInterface $entity): void
    {
        try {
            $this->_em->remove($entity);
            $this->_em->flush();
        } catch (\Throwable $e) {
            throw new DatabaseException(sprintf('Removing the %s failed', \get_class($entity)));
        }
    }

    /**
     * Save changes to the database.
     *
     * @param EntityInterface $entity Entity to persist.
     *
     * @return void
     *
     * @throws DatabaseException When saving the entity fails.
     */
    public function save(EntityInterface $entity): void
    {
        try {
            $this->_em->persist($entity);
            $this->_em->flush();
        } catch (\Throwable $e) {
            throw new DatabaseException(sprintf('Saving the %s failed', \get_class($entity)));
        }

    }

}
