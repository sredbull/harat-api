<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Response;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BaseResponse.
 */
class BaseResponse extends JsonResponse
{

    public const HTTP_CODE = Response::HTTP_OK;

    public const DEFAULT_FORMAT = 'json';

    /**
     * The serializer.
     *
     * @var Serializer $serializer
     */
    public $serializer;

    /**
     * BaseResponse constructor.
     */
    public function __construct()
    {
        $this->setSerializer();

        parent::__construct([], self::HTTP_CODE, []);
    }

    /**
     * Get the groups to include at the serialization process.
     *
     * @return array
     */
    public function getIncludedGroups(): array
    {
        global $kernel;
        $request = $kernel->getContainer()->get('request_stack');

        $includedGroups = null;

        if ($request->getCurrentRequest() !== null) {
            $includedGroups = $request->getCurrentRequest()->get('includes');
        }

        if ($includedGroups === null) {
            return [];
        }

        return explode(',', $includedGroups);
    }

    /**
     * Set the serializer.
     *
     * @return void
     */
    public function setSerializer(): void
    {
        $serializer = SerializerBuilder::create()
            ->setCacheDir(str_replace('/src/Response', '', __DIR__) . '/var/cache/' . getenv('APP_ENV') . '/jms_serializer')
            ->setDebug(getenv('APP_ENV') === 'dev')
            ->build()
        ;

        $this->serializer = $serializer;
    }

    /**
     * Set the data to serialize.
     *
     * @param mixed|array $data   The data.
     * @param array       $groups The groups to serialize.
     *
     * @return void
     */
    public function setData($data = [], array $groups = []): void
    {
        $groups[] = 'Default';
        $groups = array_merge($groups, $this->getIncludedGroups());

        $json = $this->serializer->serialize($data, self::DEFAULT_FORMAT, SerializationContext::create()->setGroups($groups));

        $this->setJson($json);
    }

}
