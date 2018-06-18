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

use App\Entity\CharacterEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMInvalidArgumentException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class CharacterRepository
 *
 * @method CharacterEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method CharacterEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method CharacterEntity[]    findAll()
 * @method CharacterEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CharacterRepository extends ServiceEntityRepository
{

    /**
     * CharacterRepository constructor.
     *
     * @param RegistryInterface $registry The registry interface.
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CharacterEntity::class);
    }

    /**
     * Save changes to the database.
     *
     * @param CharacterEntity $entity Entity to persist.
     *
     * @throws ORMInvalidArgumentException Throws an ORMInvalidArgumentException when an incorrect argument was provided.
     *
     * @return void
     */
    public function save(CharacterEntity $entity): void
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }

}
