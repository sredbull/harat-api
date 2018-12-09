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

use App\Exception\ApiException;
use App\Provider\ExpressionLanguage\ExpressionLanguageProvider;
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

    /**
     * The includes.
     *
     * @var array $includes
     */
    private $includes;

    /**
     * The serializer.
     *
     * @var Serializer $serializer
     */
    public $serializer;

    /**
     * BaseResponse constructor.
     *
     * @throws ApiException WHen setting the includes fails.
     */
    public function __construct()
    {
        $this->setIncludes();
        $this->setSerializer();

        parent::__construct([], self::HTTP_CODE, []);
    }

    /**
     * Set the includes.
     *
     * @return void
     *
     * @throws ApiException When the includes passed are not array values.
     */
    public function setIncludes(): void
    {
        global $kernel;
        $request = $kernel->getContainer()->get('request_stack');

        $includes = null;

        if ($request->getCurrentRequest() !== null) {
            $includes = $request->getCurrentRequest()->get('includes');
        }

        if ($includes === null) {
            $this->includes = [];

            return;
        }

        if (\is_array($includes) === false) {
            throw new ApiException('Includes should always be passed as array values. e.g. ?includes[]=default', Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $this->includes = $includes;
    }

    /**
     * Set the serializer.
     *
     * @return void
     */
    public function setSerializer(): void
    {
        $expressionLanguageProvider = new ExpressionLanguageProvider($this->includes);
        $expressionEvaluator = $expressionLanguageProvider->getExpressionEvaluator();

        $serializer = SerializerBuilder::create()
            ->setExpressionEvaluator($expressionEvaluator)
            ->addMetadataDir(str_replace('Response', '', __DIR__) . 'Resources/FOSUserBundle/serializer', 'FOS\\UserBundle')
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
     * @param string      $format The format to serialize to.
     *
     * @return void
     */
    public function setData($data = [], string $format = 'json'): void
    {
        $json = $this->serializer->serialize($data, $format);

        $this->setJson($json);
    }

}
