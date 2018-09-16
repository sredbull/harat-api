<?php declare (strict_types = 1);

/*
 * This file is part of the House Aratus package.
 *
 * (c) Sven Roodbol <roodbol.sven@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Jobs\View;

use App\Exception\ApiException;
use App\Jobs\BaseJob;
use App\Provider\ExpressionLanguage\ExpressionLanguageProvider;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class JsonResponseJob.
 */
class JsonResponseJob extends BaseJob
{
    /**
     * The response code to return.
     *
     * @var int $code
     */
    private $code;

    /**
     * The data to serialize.
     *
     * @var mixed $data
     */
    private $data;

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
    private $serializer;

    /**
     * JsonResponseJob constructor.
     *
     * @param mixed   $data The data.
     * @param integer $code The response code.
     */
    public function __construct($data, int $code)
    {
        $this->data = $data;
        $this->code = $code;
    }

    /**
     * Handle the job.
     *
     * @return JsonResponse
     *
     * @throws ApiException When the includes could not be set.
     */
    public function handle(): JsonResponse
    {
        $this->setIncludes($this->container->get('request_stack'));
        $this->setSerializer();

        return $this->getView($this->data, $this->code);
    }


    /**
     * Set the includes.
     *
     * @param RequestStack $request The request stack.
     *
     * @return void
     *
     * @throws ApiException When the includes passed are not array values.
     */
    public function setIncludes(RequestStack $request): void
    {
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
            ->addMetadataDir($this->container->get('kernel')->getRootDir() . '/Resources/FOSUserBundle/serializer', 'FOS\\UserBundle')
            ->build();

        $this->serializer = $serializer;
    }

    /**
     * Get the view.
     *
     * @param mixed   $data   The data to serialize.
     * @param integer $status The status code.
     * @param string  $format The format to serialize to.
     *
     * @return JsonResponse
     */
    public function getView($data, int $status, string $format = 'json'): JsonResponse
    {
        $json = $this->serializer->serialize($data, $format);

        return new JsonResponse($json, $status, [], true);
    }

}
