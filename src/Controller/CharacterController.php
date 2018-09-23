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
use App\Service\CharacterService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CharacterController.
 */
class CharacterController extends BaseController
{

    /**
     * Get a character.
     *
     * @param CharacterEntity|null $character The character.
     *
     * @Route("/character/{id}", methods={"GET"})
     *
     * @return JsonResponse
     *
     * @throws CharacterNotFoundException When the character could not be found.
     */
    public function getCharacter(?CharacterEntity $character): JsonResponse
    {
        if ($character === null) {
            throw new CharacterNotFoundException();
        }

        return $this->view($character, Response::HTTP_OK);
    }

    /**
     * Delete a character.
     *
     * @param CharacterService     $characterService The character service.
     * @param CharacterEntity|null $character        The character.
     *
     * @Route("/character/{id}", methods={"DELETE"})
     *
     * @return JsonResponse
     *
     * @throws CharacterNotFoundException When the character could not be found.
     * @throws DatabaseException          When the character could not be removed.
     */
    public function deleteCharacter(CharacterService $characterService, ?CharacterEntity $character): JsonResponse
    {
        if ($character === null) {
            throw new CharacterNotFoundException();
        }

        $characterService->remove($character);

        return $this->view(
            null,
            Response::HTTP_NO_CONTENT
        );
    }

}
