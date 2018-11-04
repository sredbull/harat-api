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

use App\Entity\RefreshTokenEntity;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class RefreshTokenRepository.
 *
 * @method RefreshTokenEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method RefreshTokenEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method RefreshTokenEntity[]    findAll()
 * @method RefreshTokenEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RefreshTokenRepository extends BaseRepository
{

    /**
     * RefreshTokenRepository constructor.
     *
     * @param RegistryInterface $registry The registry interface.
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RefreshTokenEntity::class);
    }

    /**
     * Find a refresh token by token.
     *
     * @param string $token The token.
     *
     * @return RefreshTokenEntity|null
     */
    public function findByToken(string $token): ?RefreshTokenEntity
    {
        return $this->findOneBy([
            'refreshToken' => $token,
        ]);
    }

}
