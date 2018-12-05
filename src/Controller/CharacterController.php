<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Controller;

use App\Entity\CharacterEntity;
use App\Exception\CharacterNotFoundException;
use App\Exception\DatabaseException;
use App\Response\Character\GetCharacterResponse;
use App\Response\Character\RemoveCharacterResponse;
use App\Service\CharacterService;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CharacterController.
 */
class CharacterController extends BaseController
{

    /**
     * Get a character.
     *
     * @param CharacterEntity|null $character The character id.
     * @param GetCharacterResponse $response  The response.
     *
     * @Route("/character/{character}", methods={"GET"})
     *
     * @return GetCharacterResponse
     *
     * @throws CharacterNotFoundException When the character could not be found.
     */
    public function getCharacter(?CharacterEntity $character, GetCharacterResponse $response): GetCharacterResponse
    {
        if ($character === null) {
            throw new CharacterNotFoundException();
        }

        return $response->getResponse($character);
    }

    /**
     * Remove a character.
     *
     * @param CharacterService        $characterService The character service.
     * @param CharacterEntity|null    $character        The character.
     * @param RemoveCharacterResponse $response         The response.
     *
     * @Route("/character/{id}", methods={"DELETE"})
     *
     * @return RemoveCharacterResponse
     *
     * @throws CharacterNotFoundException When the character could not be found.
     * @throws DatabaseException          When the character could not be removed.
     */
    public function removeCharacter(CharacterService $characterService, ?CharacterEntity $character, RemoveCharacterResponse $response): RemoveCharacterResponse
    {
        if ($character === null) {
            throw new CharacterNotFoundException();
        }

        $characterService->remove($character);

        return $response->getResponse([]);
    }

}
