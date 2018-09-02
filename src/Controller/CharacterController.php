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

use App\Exception\CharacterNotFoundException;
use App\Repository\CharacterRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CharacterController.
 */
class CharacterController extends FOSRestController implements ClassResourceInterface
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
    ){
        $this->characterRepository = $characterRepository;
    }

    /**
     * List an character.
     *
     * @param string $characterId The id of the character to retrieve.
     *
     * @Rest\Get("character/{characterId}")
     *
     * @return View
     *
     * @throws CharacterNotFoundException Thrown when the character could not be found.
     */
    public function getAction(string $characterId): View
    {
        $character = $this->characterRepository->find($characterId);

        if ($character === null) {
            throw new CharacterNotFoundException();
        }

        return $this->view(
            $character,
            Response::HTTP_OK
        );
    }

    /**
     * Deletes an character.
     *
     * @param string $characterId The character id to delete.
     *
     * @Rest\Delete("character/{characterId}")
     *
     * @return View
     *
     * @throws CharacterNotFoundException Thrown when the character could not be found.
     */
    public function deleteAction(string $characterId): View
    {
        $existingCharacter = $this->characterRepository->find($characterId);

        if ($existingCharacter === null) {
            throw new CharacterNotFoundException();
        }

        $this->characterRepository->delete($existingCharacter);

        return $this->view(
            null,
            Response::HTTP_NO_CONTENT
        );
    }

}
