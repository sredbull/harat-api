<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Interfaces;

/**
 * Interface RepositoryInterface
 */
interface RepositoryInterface
{

    /**
     * @param EntityInterface $entity The entityInterface.
     *
     * @return void
     */
    public function remove(EntityInterface $entity): void;

    /**
     * @param EntityInterface $entity The entityInterface.
     *
     * @return void
     */
    public function persist(EntityInterface $entity): void;

    /**
     * @return void
     */
    public function flush(): void;

    /**
     * @param EntityInterface $entity The entityInterface.
     *
     * @return void
     */
    public function save(EntityInterface $entity): void;

}
