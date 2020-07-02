<?php
declare(strict_types=1);

namespace App\Module\Resource\Controller;

use App\Controller\RestControllerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\Resource\Storage\ResourceStorageInterface;
use App\Storage\StorageInterface;
use Laminas\InputFilter\InputFilterInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class ResourceController
 * @package App\Module\Resource\Controller
 */
class ResourceController implements RestControllerInterface {

    use AcceptServiceAwareTrait;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $hydratorService = 'RestResourceEntityHydrator';


    /**
     * ResourceController constructor.
     * @param ResourceStorageInterface $storage
     */
    public function __construct(ResourceStorageInterface $storage, ContainerInterface $container ) {
        $this->storage = $storage;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function post(Request $request, Response $response) {

        $data = array_merge($request->getParsedBody(), $request->getUploadedFiles());

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

        $file = $request->getUploadedFiles()['file'];
        $data = $request->getParsedBody();
        unset( $data['dimension'] ); // TODO remove when add validation
        $data['size'] = $file->getSize();
        $data['mimeType'] = $file->getClientMediaType();
        $data['src'] = $file->getStream()->getMetadata('uri');

        $entity = $this->storage->getHydrator()->hydrate($data, $this->storage->getEntityPrototype()->getPrototype($data));

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

        $data = array_merge($request->getParsedBody(), $request->getUploadedFiles());

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


        $putEntity = $this->storage->getEntityPrototype()->getPrototype($data);
        var_dump($putEntity);
   //     $putEntity->setId($id);
       // $this->storage->update($putEntity);
        //var_dump($this->storage->getEntityPrototype());
        //var_dump($putEntity);
        var_dump(__FUNCTION__);
        die();
    }

    public function patch(Request $request, Response $response) {
        throw new \Exception('TODO implements');
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
    public function paginate(Request $request, Response $response) {
        $query = $request->getQueryParams();
        $page = isset($query['page']) ? intval($query['page']) ? intval($query['page']) : 1 : 1;
        $itemPerPage = isset($query['item-per-page']) ? intval($query['item-per-page']) ? intval($query['item-per-page']) : 10 : 10;
        $pagination = $this->storage->getPage($page, $itemPerPage);

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