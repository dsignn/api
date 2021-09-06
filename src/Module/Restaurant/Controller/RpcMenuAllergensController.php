<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Controller;


use App\Controller\RpcControllerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\Restaurant\Storage\MenuAllergensStorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class RpcMenuAllergensController
 * @package App\Module\Restaurant\Controller
 */
class RpcMenuAllergensController implements RpcControllerInterface {

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
     * @param MenuAllergensStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(MenuAllergensStorageInterface $storage, ContainerInterface $container) {

        $this->storage = $storage;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {

        $params = $request->getQueryParams();
        $allergens = $this->storage->getAll($params, ["allergens.order" => 1])->current();

        foreach ($allergens->allergens as $value) {
            $allergens->{$value["name"]} = $value['translation'];
        }

        unset($allergens->{'allergens'});

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $allergens);
    }
}