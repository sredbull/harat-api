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
use App\Interfaces\RepositoryInterface;
use App\Provider\ExpressionLanguage\ExpressionLanguageProvider;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class UserController.
 */
class BaseController extends FOSRestController
{

    /**
     * The Doctrine registry manager.
     *
     * @var RegistryInterface $registryManager
     */
    private $registryManager;

    /**
     * The includes.
     *
     * @var array $includes
     */
    private $includes;

    /**
     * The repository.
     *
     * @var RepositoryInterface $repository
     */
    private $repository;

    /**
     * The request stack.
     *
     * @var RequestStack $requestStack
     */
    private $requestStack;

    /**
     * The serializer.
     *
     * @var Serializer
     */
    private $serializer;

    /**
     * BaseController constructor.
     *
     * @param RegistryInterface $registryManager The Doctrine registry manager.
     * @param RequestStack      $requestStack    The request stack.
     *
     * @throws ApiException Thrown when setting the includes fails.
     */
    public function __construct(RegistryInterface $registryManager, RequestStack $requestStack)
    {
        $this->registryManager = $registryManager;
        $this->requestStack = $requestStack;
        $this->setRepository();
        $this->setIncludes();
        $this->setSerializer();
    }

    /**
     * Get the repository based on the controller class name.
     *
     * @return RepositoryInterface|null
     */
    public function getRepository(): ?RepositoryInterface
    {
        return $this->repository;
    }

    /**
     * Set the repository based on the controller name.
     *
     * @return void
     */
    public function setRepository(): void
    {
        $this->repository = null;
        $className = str_replace('Controller', 'Repository', static::class);
        if (class_exists($className) === true) {
            $this->repository = new $className($this->registryManager);
        }
    }

    /**
     * Get the includes
     *
     * @return array
     */
    public function getIncludes(): array
    {
        return $this->includes;
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
        $includes = null;

        if ($this->getRequestStack()->getCurrentRequest() !== null) {
            $includes = $this->getRequestStack()->getCurrentRequest()->get('includes');
        }

        if ($includes === null) {
            $this->includes = [];

            return;
        }

        if (\is_array($includes) === false) {
            throw new ApiException('Includes should always be passed as array values. e.g. ?includes[]=default');
        }

        $this->includes = $includes;
    }

    /**
     * Get the serializer.
     *
     * @return Serializer
     */
    public function getSerializer(): Serializer
    {
        return $this->serializer;
    }

    /**
     * Set the serializer.
     *
     * @return void
     */
    public function setSerializer(): void
    {
        $expressionLanguageProvider = new ExpressionLanguageProvider($this->getIncludes());
        $expressionEvaluator = $expressionLanguageProvider->getExpressionEvaluator();
        $serializer = SerializerBuilder::create()
            ->setExpressionEvaluator($expressionEvaluator)
            ->addMetadataDir(str_replace('Controller', '', __DIR__) . 'Resources/FOSUserBundle/serializer', 'FOS\\UserBundle')
            ->build();

        $this->serializer = $serializer;
    }

    /**
     * Get the request stack.
     *
     * @return RequestStack
     */
    public function getRequestStack(): RequestStack
    {
        return $this->requestStack;
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
