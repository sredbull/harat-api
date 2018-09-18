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

use App\Exception\ApiException;
use App\Provider\ExpressionLanguage\ExpressionLanguageProvider;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController.
 */
class BaseController extends Controller
{

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
     * @param RequestStack $request The request.
     *
     * @throws ApiException WHen setting the includes fails.
     */
    public function __construct(RequestStack $request)
    {
        $this->setIncludes($request);
        $this->setSerializer();
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
            ->addMetadataDir(str_replace('Controller', '', __DIR__) . 'Resources/FOSUserBundle/serializer', 'FOS\\UserBundle')
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
    public function view($data, int $status, string $format = 'json'): JsonResponse
    {
        $json = $this->serializer->serialize($data, $format);

        return new JsonResponse($json, $status, [], true);
    }

}
