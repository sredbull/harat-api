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

use App\Entity\CharacterEntity;
use App\Exception\DatabaseException;
use App\Repository\CharacterRepository;

/**
 * Class CharacterController.
 */
class CharacterService
{

    /**
     * The characterRepository.
     *
     * @var CharacterRepository $characterRepository
     */
    private $characterRepository;

    /**
     * CharacterController constructor.
     *
     * @param CharacterRepository $characterRepository The characterRepository.
     */
    public function __construct(
        CharacterRepository $characterRepository
    )
    {
        $this->characterRepository = $characterRepository;
    }

    /**
     * Remove the character.
     *
     * @param CharacterEntity $character The character.
     *
     * @return void
     *
     * @throws DatabaseException When removing the entity fails.
     */
    public function remove(CharacterEntity $character): void
    {
        $this->characterRepository->remove($character);
    }

}
