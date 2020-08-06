<?php
declare(strict_types=1);

namespace App\Module\Resource\Controller;

use App\Controller\RestControllerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\Resource\Entity\AbstractResourceEntity;
use App\Module\Resource\Entity\Embedded\Dimension;
use App\Module\Resource\Entity\ImageResourceEntity;
use App\Module\Resource\Entity\VideoResourceEntity;
use App\Module\Resource\Storage\ResourceStorageInterface;
use App\Storage\StorageInterface;
use Laminas\InputFilter\InputFilterInterface;
use Notihnio\RequestParser\RequestParser;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function DI\value;

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
    protected $tmp;

    /**
     * @var string
     */
    protected $hydratorService = 'RestResourceEntityHydrator';


    /**
     * ResourceController constructor.
     * @param ResourceStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(ResourceStorageInterface $storage, ContainerInterface $container ) {
        $this->storage = $storage;
        $this->container = $container;
        $this->tmp = $container->get('settings')['tmp'];
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
        // TODO REMOVE
        $data['size'] = filesize($data['file']['src']);
        $data['mimeType'] = $data['file']['mimeType'];
        $data['src'] = $data['file']['src'];
        unset($data['file']);

        $entity = $this->storage->getHydrator()->hydrate(
            $data,
            $this->storage->getEntityPrototype()->getPrototype($data)
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

        $requestParams = RequestParser::parse();
        $data = array_merge($requestParams->files, $requestParams->params);

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
        // TODO REMOVE
        $data['size'] = filesize($data['file']['src']);
        $data['mimeType'] = $data['file']['mimeType'];
        $data['src'] = $data['file']['src'];
        unset($data['file']);

        $putEntity = $this->storage->getEntityPrototype()->getPrototype($data);
        $oldEntityData = $this->storage->getHydrator()->extract($entity);

        $this->storage->getHydrator()->hydrate($oldEntityData, $putEntity);
        $this->storage->getHydrator()->hydrate($data, $putEntity);
        $this->storage->update($putEntity);

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $putEntity);
    }

    public function patch(Request $request, Response $response) {

        $id = $request->getAttribute('__route__')->getArgument('id');
        /** @var AbstractResourceEntity $entity */
        $entity = $this->storage->get($id);

        if (!$entity) {
            return $response->withStatus(404);
        }

        $requestParams = RequestParser::parse();
        $data = array_merge($requestParams->files, $requestParams->params);

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

            $dataFilter = $validator->getValues();
            foreach ($dataFilter as $key => $value) {
                if(!isset($data[$key])) {
                    unset($dataFilter[$key]);
                }

            }
            $data = $dataFilter;
        }

        if (isset($data['file'])) {
            // TODO REMOVE
            $data['size'] = filesize($data['file']['src']);
            $data['mimeType'] = $data['file']['mimeType'];
            $data['src'] = $data['file']['src'];
            unset($data['file']);
        }

        /** @var AbstractResourceEntity $dataEntity */
        $dataEntity = $this->storage->getEntityPrototype()->getPrototype($data);
        if ($dataEntity !== null && !($dataEntity instanceof $entity)) {
            $this->storage->getHydrator()->hydrate($data, $dataEntity);
            $dataEntity->setId($entity->getId());
            $dataEntity->setS3path($entity->getS3path());
            $dataEntity->setOrganizationReference($entity->getOrganizationReference());
            $entity = $dataEntity;
        } else {
            $data['mimeType'] = $entity->getMimeType();
            $this->storage->getHydrator()->hydrate($data, $entity);
        }

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
        return $response->withStatus(200);
    }
}