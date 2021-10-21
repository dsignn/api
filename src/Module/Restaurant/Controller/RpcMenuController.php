<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Controller;

use App\Controller\RpcControllerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\Organization\Storage\OrganizationStorageInterface;
use App\Module\Restaurant\Entity\MenuEntity;
use App\Module\Restaurant\Storage\MenuStorage;
use App\Module\Restaurant\Storage\MenuStorageInterface;
use App\Storage\StorageInterface;
use MongoDB\BSON\ObjectId;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

/**
 * Class RpcMenuController
 * @package App\Module\Restaurant\Controller
 */
class RpcMenuController implements RpcControllerInterface {

    use AcceptServiceAwareTrait;

    /**
     * @var string
     */
    protected $hydratorService = 'RestMenuEntityWithResourceHydrator';

    /**
     * @var Twig
     */
    protected $twig;

    /**
     * @var MenuStorage
     */
    protected $menuStorage;

    /**
     * @var StorageInterface
     */
    protected $organizationStorage;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * RpcMenuController constructor.
     * @param MenuStorageInterface $menuStorage
     * @param OrganizationStorageInterface $organizationStorage
     * @param Twig $twig
     * @param ContainerInterface $container
     */
    public function __construct(MenuStorageInterface $menuStorage,
                                OrganizationStorageInterface $organizationStorage,
                                ContainerInterface $container) {
        $this->menuStorage = $menuStorage;
        $this->organizationStorage = $organizationStorage;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {

        $slug = $request->getAttribute('__route__')->getArgument('slug');
        $query = $request->getQueryParams();
        $searchOrganization = null;
        $searchMenu = null;
        $menu = null;
        $organization = null;

        switch (true) {
            case isset($query['delivery']) === true:
                $status = MenuEntity::$STATUS_DELIVERY;
                break;
            default:
                $status = MenuEntity::$STATUS_ENABLE;
        }

        switch (true) {
            case $slug === '__previews':
                try {
                    $id = new ObjectId(isset($query['id']) ? $query['id'] : null);
                    $searchMenu = ['_id' => $id];
                } catch (\Exception $e) {
                    $searchOrganization = ['normalize_name' => $slug];
                }
                break;
            default:
                $searchOrganization = ['normalize_name' => $slug];
                break;
        }

        if ($searchMenu) {

            /** @var MenuEntity $menuEntity */
            $menuEntity = $this->menuStorage->getAll($searchMenu)->current();
            // Menu not found
            if (!$menuEntity) {
                return $this->menuNotFound($response, $request);
            }
       
            $menu = $this->menuStorage->getMenuByMenuId($menuEntity);

        } else {
       
            $organization = $this->organizationStorage->getAll($searchOrganization)->current();
            if (!$organization) {

                return $this->organizationNotFound($response, $request);
            }
            $menu = $this->menuStorage->getMenuByRestaurantSlug($slug, $status);
         
            if (!$menu) {
         
                return $this->menuNotFound($response, $request);
            }
        }

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $menu);
    }

    /**
     * @param Response $response
     * @param Request $request
     * @return Response
     * @throws \App\Middleware\ContentNegotiation\Exception\ServiceNotFound
     */
    protected function menuNotFound(Response $response, Request $request) {
        $request = $request->withHeader('error-message', 'Il ristorante non ha ancora caricato il suo menu');
        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, []);
    }

    /**
     * @param Response $response
     * @param Request $request
     * @return Response
     * @throws \App\Middleware\ContentNegotiation\Exception\ServiceNotFound
     */
    protected function organizationNotFound(Response $response, Request $request) {
        $request = $request->withHeader('error-message', 'Il ristorante che stai cercando non si è ancora registrato alla piattaforma...');
        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, []);
    }
}