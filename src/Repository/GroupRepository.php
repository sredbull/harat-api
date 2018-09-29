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

use App\Entity\GroupEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class GroupRepository.
 *
 * @method GroupEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupEntity[]    findAll()
 * @method GroupEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends BaseRepository
{

    /**
     * GroupRepository constructor.
     *
     * @param RegistryInterface $registry The registry interface.
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GroupEntity::class);
    }

    /**
     * Find all group names.
     *
     * @return array
     */
    public function findAllGroupNames(): array
    {
        $groupNames = [];
        foreach ($this->findAll() as $group) {
            $groupNames[]  = $group->getName();
        }

        return $groupNames;
    }

}
