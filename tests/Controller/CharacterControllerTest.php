<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Tests\Controller;

use App\Controller\CharacterController;
use App\Entity\CharacterEntity;
use App\Exception\ApiException;
use App\Exception\CharacterNotFoundException;
use App\Repository\CharacterRepository;
use App\Service\CharacterService;
use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class CharacterControllerTest.
 */
class CharacterControllerTest extends BaseTestCase
{

    /**
     * Test if the getCharacter method returns as expected.
     *
     * @throws ApiException               When there is an api exception.
     * @throws CharacterNotFoundException When the charatcer could not be found.
     *
     * @return void
     */
    public function testValidGetCharacter(): void
    {
        $requestStack = new RequestStack();
        $controller = new CharacterController($requestStack);

        $character = new CharacterEntity();
        $character->setCharacterName('character');
        $character->setCharacterId(1);
        $data = $controller->getCharacter($character);

        $this->assertEquals('{"character_id":1,"character_name":"character"}', $data->getContent());
        $this->assertEquals(200, $data->getStatusCode());
    }

    /**
     * Test if the getCharacter method throws an exception as expected.
     *
     * @throws ApiException               When there is an api exception.
     * @throws CharacterNotFoundException When the charatcer could not be found.
     *
     * @return void
     */
    public function testInvalidGetCharacter(): void
    {
        $this->expectException(CharacterNotFoundException::class);

        $requestStack = new RequestStack();
        $controller = new CharacterController($requestStack);

        $controller->getCharacter(null);
    }

    /**
     * Test if the getCharacter method returns as expected.
     *
     * @throws ApiException               When there is an api exception.
     * @throws CharacterNotFoundException When the charatcer could not be found.
     *
     * @return void
     */
    public function testValidRemoveCharacter(): void
    {
        $requestStack = new RequestStack();
        $controller = new CharacterController($requestStack);
        $service = $this->createMock(CharacterService::class);


        $character = new CharacterEntity();
        $character->setCharacterName('character');
        $character->setCharacterId(1);

        $service->expects($this->once())
            ->method('remove')
            ->with($character);

        $data = $controller->removeCharacter($service, $character);

        $this->assertEquals('null', $data->getContent());
        $this->assertEquals(204, $data->getStatusCode());
    }

    /**
     * Test if the removeCharacter method throws an exception as expected.
     *
     * @throws ApiException               When there is an api exception.
     * @throws CharacterNotFoundException When the charatcer could not be found.
     *
     * @return void
     */
    public function testInvalidRemoveCharacter(): void
    {
        $this->expectException(CharacterNotFoundException::class);

        $requestStack = new RequestStack();
        $controller = new CharacterController($requestStack);
        $service = new CharacterService($this->createMock(CharacterRepository::class));

        $controller->removeCharacter($service, null);
    }

}
