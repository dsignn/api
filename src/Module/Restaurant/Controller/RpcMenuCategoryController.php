<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Controller;


use App\Controller\RpcControllerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\Restaurant\Storage\MenuCategoryStorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class MenuCategoryController
 * @package App\Module\Restaurant\Controller
 */
class RpcMenuCategoryController implements RpcControllerInterface {

    use AcceptServiceAwareTrait;

    /**
     * @var string
     */
    protected $hydratorService = 'RpcMenuCategoryEntityHydrator';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * RpcMenuCategoryController constructor.
     * @param MenuCategoryStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(MenuCategoryStorageInterface $storage, ContainerInterface $container) {

        $this->storage = $storage;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {

        $params = $request->getQueryParams();
        $categories = $this->storage->getAll($params);

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $categories);
    }
}