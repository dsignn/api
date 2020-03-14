<?php
declare(strict_types=1);

namespace App\Controller;

use App\Middleware\ContentNegotiation\ContentType\ContentTypeTransformInterface;
use App\Middleware\ContentNegotiation\Exception\ServiceNotFound;
use App\Storage\StorageInterface;
use Laminas\InputFilter\InputFilterInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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

    /**
     * @var ContainerInterface
     */
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
     * @throws ServiceNotFound
     */
    public function get(Request $request, Response $response) {

        $id = $request->getAttribute('__route__')->getArgument('id');
        $entity = $this->storage->get($id);

        if (!$entity) {
            return $response->withStatus(404);
        }

        $contentTypeService = $this->getContentTypeService($request);
        return $contentTypeService->transformContentType($response, $entity);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ServiceNotFound
     */
    public function post(Request $request, Response $response) {

        $data = $request->getParsedBody();

        if ($request->getAttribute('app-validation')) {
            /** @var InputFilterInterface $validator */
            $validator = $request->getAttribute('app-validation');
            $validator->setData($data);
            if (!$validator->isValid()) {
                $contentTypeService = $this->getContentTypeService($request);
                $response = $contentTypeService->transformContentType(
                    $response,
                    ['errors' => $validator->getMessages()]
                );
                return $response->withStatus(422);
            }

            $data = $validator->getValues();
        }

        $entity = $this->storage->save($data);

        $contentTypeService = $this->getContentTypeService($request);
        return $contentTypeService->transformContentType($response, $entity);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ServiceNotFound
     */
    public function put(Request $request, Response $response) {

        $id = $request->getAttribute('__route__')->getArgument('id');
        $entity = $this->storage->get($id);

        if (!$entity) {
            return $response->withStatus(404);
        }

        /**
         * TODO override total entity?? REST complient
         */
        $putEntity = clone $this->storage->getObjectPrototype();
        $this->storage->getHydrator()->hydrate($request->getParsedBody(), $putEntity);
        $putEntity->setId($id);

        $this->storage->update($putEntity);

        $contentTypeService = $this->getContentTypeService($request);
        return $contentTypeService->transformContentType($response, $putEntity);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ServiceNotFound
     */
    public function patch(Request $request, Response $response) {

        $id = $request->getAttribute('__route__')->getArgument('id');
        $entity = $this->storage->get($id);

        if (!$entity) {
            return $response->withStatus(404);
        }

        /**
         * TODO override total entity?? REST complient
         */
        $this->storage->getHydrator()->hydrate($request->getParsedBody(), $entity);
        $this->storage->update($entity);

        $contentTypeService = $this->getContentTypeService($request);
        return $contentTypeService->transformContentType($response, $entity);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function delete(Request $request, Response $response) {

        $id = $request->getAttribute('__route__')->getArgument('id');
        $entity = $this->storage->get($id);

        if (!$entity) {
            return $response->withStatus(404);
        }

        $this->storage->delete($id);
        return $response->withStatus(200);
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

        $contentTypeService = $this->getContentTypeService($request);
        return $contentTypeService->transformContentType($response, $pagination);
    }

    /**
     * @param Request $request
     * @return ContentTypeTransformInterface
     * @throws ServiceNotFound
     */
    protected function getContentTypeService(Request $request) {

        /** @var ContentTypeTransformInterface $contentTypeService */
        $contentTypeService = $request->getAttribute('ContentTypeService');

        if (!$contentTypeService) {
            throw new ServiceNotFound('ContentTypeService not found in request attribute');
        }

        if ($this->container->has('Rest' . $this->entityNameClass . 'Hydrator')) {
            $contentTypeService->setHydrator($this->container->get('Rest' . $this->entityNameClass . 'Hydrator'));
        }

        return $contentTypeService;
    }
}