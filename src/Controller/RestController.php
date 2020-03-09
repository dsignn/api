<?php
declare(strict_types=1);

namespace App\Controller;

use App\Middleware\ContentNegotiation\ContentType\ContentTypeTransformInterface;
use App\Middleware\ContentNegotiation\Exception\ServiceNotFound;
use App\Storage\StorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Hydrator\HydratorAwareInterface;

/**
 * Class RestController
 * @package App\Controller
 */
class RestController
{
    /**
     * @var string
     */
    protected $entityNameClass = '';

    /**
     * @var StorageInterface
     */
    protected $storage;

    protected $container;

    /**
     * RestController constructor.
     * @param StorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(StorageInterface $storage, ContainerInterface $container) {
        $this->storage = $storage;
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function get(Request $request, Response $response) {

        $id = $request->getAttribute('__route__')->getArgument('id');

        $entity = $this->storage->get($id);

        if ($this->storage instanceof HydratorAwareInterface) {
            $entity = $entity  ? $this->storage->getHydrator()->extract($entity) : null;
        }

        $response->getBody()->write(json_encode($entity));
        return $response->withStatus(200);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function post(Request $request, Response $response) {
        return $response->withStatus(405);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function put(Request $request, Response $response) {
        return $response->withStatus(405);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function patch(Request $request, Response $response) {
        return $response->withStatus(405);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function delete(Request $request, Response $response) {
        return $response->withStatus(405);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ServiceNotFound
     */
    public function paginate(Request $request, Response $response) {

        $query = $request->getQueryParams();
        $page = isset($query['page']) ? intval($query['page']) ? intval($query['page']) : 1 : 1;
        $itemPerPage = isset($query['item-per-page']) ? intval($query['item-per-page']) ? intval($query['item-per-page']) : 10 : 10;
        $pagination = $this->storage->getPage($page, $itemPerPage);

        /** @var ContentTypeTransformInterface $contentTypeService */
        $contentTypeService = $request->getAttribute('ContentTypeService');

        if (!$contentTypeService) {
            throw new ServiceNotFound('ContentTypeService not found in request attribute');
        }

        if ($this->container->has('Rest' . $this->entityNameClass . 'Hydrator')) {
            $contentTypeService->setHydrator($this->container->get('Rest' . $this->entityNameClass . 'Hydrator'));
        }

        return $contentTypeService->transformContentType($response, $pagination);
    }
}