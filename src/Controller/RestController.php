<?php
declare(strict_types=1);

namespace App\Controller;

use App\Storage\Event\PreProcess;
use App\Storage\StorageInterface;
use Laminas\InputFilter\InputFilterInterface;
use Notihnio\RequestParser\RequestParser;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use function DI\get;

/**
 * Class RestController
 * @package App\Controller
 */
class RestController implements RestControllerInterface {

    /**
     * @var string
     */
    static public $PREPROCESS_POST = 'preprocess_post';

    /**
     * @var string
     */
    static public $PREPROCESS_PATCH = 'preprocess_patch';

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

        return $this->getAcceptData($request, $response, $entity);
    }

    /**
     * @inheritDoc
     */
    public function post(Request $request, Response $response) {

        $data = $this->getData($request);

        if ($request->getAttribute('app-validation')) {
           
            /** @var InputFilterInterface $validator */
            $validator = $request->getAttribute('app-validation');
            $validator->setData($data);

            if (!$validator->isValid()) {
                $response = $response->withStatus(422);
                return $this->getAcceptData($request, $response, ['errors' => $validator->getMessages()]);
            }

            $data = $validator->getValues();
        }

   
        $entity = $this->storage->getEntityPrototype()->getPrototype($data);
   
        $preprocess = new PreProcess($entity, $data);
        $this->storage->getEventManager()->trigger(RestController::$PREPROCESS_POST, $preprocess);
        $data = $preprocess->getData();

        $this->storage->getHydrator()->hydrate($data, $entity); 
        $this->storage->save($entity);
    
        return $this->getAcceptData($request, $response, $entity);
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

        $data = $this->getData($request);

        if ($request->getAttribute('app-validation')) {
            /** @var InputFilterInterface $validator */
            $validator = $request->getAttribute('app-validation');
            $validator->setData($data);
            if (!$validator->isValid()) {
                $response = $response->withStatus(422);
                return $this->getAcceptData($request, $response, ['errors' => $validator->getMessages()]);
            }
            $data = $validator->getValues();
        }

        //$putEntity = clone $this->storage->getEntityPrototype()->getPrototype($request->getParsedBody());
        $this->storage->getHydrator()->hydrate($data, $entity);
        //$putEntity->setId($id);

        $this->storage->update($entity);

        return $this->getAcceptData($request, $response, $entity);
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

        $data = $this->getData($request);

        if ($request->getAttribute('app-validation')) {
            /** @var InputFilterInterface $validator */
            $validator = $request->getAttribute('app-validation');
            $validator->setData($data);
            if (!$validator->isValid()) {
                $response = $response->withStatus(422);
                return $this->getAcceptData($request, $response, ['errors' => $validator->getMessages()]);
            }

            $data = $validator->getValues();
        }

        $preprocess = new PreProcess($entity, $data);
        $this->storage->getEventManager()->trigger(
            RestController::$PREPROCESS_PATCH,
            $preprocess
        );

        $data = $preprocess->getData();
        $this->storage->getHydrator()->hydrate($data, $entity);
        $this->storage->update($entity);

        return $this->getAcceptData($request, $response, $entity);
    }

    /**
     * @inheritDoc
     */
    public function delete(Request $request, Response $response) {

        $id = $request->getAttribute('__route__')->getArgument('id');

        if (!$this->storage->delete($id)) {
            return $response->withStatus(404);
        }

        return $response->withStatus(200);
    }

    /**
     * @inheritDoc
     */
    public function paginate(Request $request, Response $response) {

        $filter = $request->getAttribute('app-query-string');
        $query =  array_merge($filter ? $filter : [], $request->getQueryParams());

        $page = isset($query['page']) ? intval($query['page']) ? intval($query['page']) : 1 : 1;
        unset($query['page']);
        $itemPerPage = isset($query['item-per-page']) ? intval($query['item-per-page']) ? intval($query['item-per-page']) : 10 : 10;
        unset($query['item-per-page']);

        $storageFilter = $request->getAttribute('app-storage-filter');
        if ($storageFilter) {
            $query = $storageFilter->computeQueryString($query);
        }

        $pagination = $this->storage->getPage($page, $itemPerPage, $query);

        return $this->getAcceptData($request, $response, $pagination);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed|Response
     */
    public function options(Request $request, Response $response) {
        return $response;
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getData(Request $request) {

        $data = array_merge(
            $request->getParsedBody() !== null ? $request->getParsedBody() : [], 
            $request->getUploadedFiles(),
            $request->getAttribute('app-body-data') ? $request->getAttribute('app-body-data') : []
        );

        if (count($data) === 0) {
            $requestParams = RequestParser::parse();
            $data = array_merge($requestParams->files, $requestParams->params);
        }

        return $data;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return void
     */
    protected function getAcceptData(Request $request, Response $response, $entity) {
        $acceptService = $request->getAttribute('app-accept-service');
        if ($acceptService) {
            return $acceptService->transformAccept($response, $entity);
        } else {
            return $response->withStatus(200);
        }
    }
}