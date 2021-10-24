<?php
declare(strict_types=1);

namespace App\Controller;

use App\InputFilter\Input;
use App\InputFilter\InputFilter;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Storage\Event\PreProcess;
use App\Storage\StorageInterface;
use Laminas\InputFilter\InputFilterInterface;
use Notihnio\RequestParser\RequestParser;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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
     *
     */
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

        $data = $this->getData($request);

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

        $preprocess = new PreProcess($entity, $data);
        $this->storage->getEventManager()->trigger(RestController::$PREPROCESS_POST, $preprocess);
        $data = $preprocess->getData();

        $this->storage->getHydrator()->hydrate($data, $entity);
        // Preprocess data we can manipulate data and entity

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

        $data = $this->getData($request);

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

        //$putEntity = clone $this->storage->getEntityPrototype()->getPrototype($request->getParsedBody());
        $this->storage->getHydrator()->hydrate($data, $entity);
        //$putEntity->setId($id);

        $this->storage->update($entity);

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $entity);
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
                $acceptService = $this->getAcceptService($request);
                $response = $acceptService->transformAccept(
                    $response,
                    ['errors' => $validator->getMessages()]
                );
                return $response->withStatus(422);
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

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $entity);
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

        $filter = $request->getAttribute('app-data-filter');
        $query =  array_merge($filter ? $filter : [], $request->getQueryParams());

        $page = isset($query['page']) ? intval($query['page']) ? intval($query['page']) : 1 : 1;
        unset($query['page']);
        $itemPerPage = isset($query['item-per-page']) ? intval($query['item-per-page']) ? intval($query['item-per-page']) : 10 : 10;
        unset($query['item-per-page']);
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

    /**
     * @param Request $request
     * @return array
     */
    protected function getData(Request $request) {

        $data = array_merge($request->getParsedBody() !== null ? $request->getParsedBody() : [], $request->getUploadedFiles());

        if (count($data) === 0) {
            $requestParams = RequestParser::parse();
            $data = array_merge($requestParams->files, $requestParams->params);
        }

        return $data;
    }
}