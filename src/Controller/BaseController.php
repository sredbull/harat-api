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

use App\Provider\ExpressionLanguage\ExpressionLanguageProvider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class UserController.
 */
class BaseController extends FOSRestController
{

    /**
     * The Doctrine entity manager.
     *
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * The repository.
     *
     * @var EntityRepository $repository
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
     * @param EntityManagerInterface $entityManager The Doctrine entity manager.
     * @param RequestStack           $requestStack  The request stack.
     */
    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
        $this->setRepository();
        $this->setSerializer();
    }

    /**
     * Get the repository based on the controller class name.
     *
     * @return EntityRepository|null
     */
    public function getRepository(): ?EntityRepository
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
        $className = str_replace('Controller', 'Entity', static::class);
        if (class_exists($className) === true) {
            $this->repository = $this->entityManager->getRepository($className);
        }
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
        $includes = null;
        if ($this->getRequestStack()->getCurrentRequest() !== null) {
            $includes = $this->getRequestStack()->getCurrentRequest()->get('includes');
        }
        if ($includes !== null) {
            $includes = json_decode($includes);
        }
        $expressionLanguageProvider = new ExpressionLanguageProvider($includes);
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
