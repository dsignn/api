<?php
declare(strict_types=1);

namespace App\Controller;

use App\Storage\StorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class AllRpcController
 * @package App\Controller
 */
class AllRpcController implements RpcControllerInterface {

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var StorageInterface
     */
    protected $container;

    /**
     * AllRpcController constructor.
     * @param StorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(StorageInterface $storage, ContainerInterface $container) {
        $this->storage = $storage;
        $this->container= $container;
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {

        $filter = $request->getAttribute('app-query-string') ? $request->getAttribute('app-query-string') : [];
        $query = array_merge($filter, $request->getQueryParams());

        $storageFilter = $request->getAttribute('app-storage-filter');
        if ($storageFilter) {
            $query = $storageFilter->computeQueryString($query);
        }

        $search = $this->storage->getAll($query, []);
        $acceptService = $this->getAcceptService($request);

        return $acceptService->transformAccept($response, $search);
    }
}