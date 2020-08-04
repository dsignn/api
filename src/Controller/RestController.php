<?php
declare(strict_types=1);

namespace App\Controller;

use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Storage\Event\PreProcess;
use App\Storage\Storage;
use App\Storage\StorageInterface;
use Laminas\InputFilter\InputFilterInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class RestController
 * @package App\Controller
 */
class RestController implements RestControllerInterface
{
    use AcceptServiceAwareTrait;

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
     * @inheritDoc
     */
    public function get(Request $request, Response $response) {

        $id = $request->getAttribute('__route__')->getArgument('id');
        $entity = $this->storage->get($id);

        if (!$entity) {
            return $response->withStatus(404);
        }

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $entity);
    }

    /**
     * @inheritDoc
     */
    public function post(Request $request, Response $response) {

        $data = $request->getParsedBody() ? $request->getParsedBody() : [];

        if ($request->getAttribute('app-validation')) {
            /** @var InputFilterInterface $validator */
            $validator = $request->getAttribute('app-validation');
            $validator->setData($data);
            if (!$validator->isValid()) {
                $acceptService = $this->getAcceptService($request);
                $response = $acceptService->transformAccept(
                    $response,
                    ['errors' => $validator->getMessages()]
                );
                return $response->withStatus(422);
            }

            $data = $validator->getValues();
        }

        $entity = $this->storage->getEntityPrototype()->getPrototype($data);
        $this->storage->getHydrator()->hydrate($data, $entity);
        // Preprocess data we can manipulate data and entity
        $this->storage->getEventManager()->trigger(
            Storage::$PREPROCESS_SAVE,
            new PreProcess($entity, $data)
        );

        $this->storage->save($entity);
        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $entity);
    }

    /**
     * @inheritDoc
     */
    public function put(Request $request, Response $response) {

        $id = $request->getAttribute('__route__')->getArgument('id');
        $entity = $this->storage->get($id);

        if (!$entity) {
            return $response->withStatus(404);
        }

        $data = $request->getParsedBody();

        if ($request->getAttribute('app-validation')) {
            /** @var InputFilterInterface $validator */
            $validator = $request->getAttribute('app-validation');
            $validator->setData($data);
            if (!$validator->isValid()) {
                $acceptService = $this->getAcceptService($request);
                $response = $acceptService->transformAccept(
                    $response,
                    ['errors' => $validator->getMessages()]
                );
                return $response->withStatus(422);
            }

            $data = $validator->getValues();
        }

        $putEntity = clone $this->storage->getEntityPrototype()->getPrototype($request->getParsedBody());
        $this->storage->getHydrator()->hydrate($data, $putEntity);
        $putEntity->setId($id);

        $this->storage->update($putEntity);

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $putEntity);
    }

    /**
     * @inheritDoc
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

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $entity);
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function paginate(Request $request, Response $response) {

        $filter = $request->getAttribute('app-data-filter');
        $query =  array_merge($filter ? $filter : [], $request->getQueryParams());
        $page = isset($query['page']) ? intval($query['page']) ? intval($query['page']) : 1 : 1;
        $itemPerPage = isset($query['item-per-page']) ? intval($query['item-per-page']) ? intval($query['item-per-page']) : 10 : 10;
        $pagination = $this->storage->getPage($page, $itemPerPage, $query);

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $pagination);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed|Response
     */
    public function options(Request $request, Response $response) {
        return $response;
    }
}