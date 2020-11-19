<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Controller;

use App\Controller\RpcControllerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\Organization\Storage\OrganizationStorage;
use App\Module\Organization\Storage\OrganizationStorageInterface;
use App\Module\Resource\Storage\ResourceStorageInterface;
use App\Module\Restaurant\Entity\Embedded\MenuItem;
use App\Module\Restaurant\Storage\MenuStorage;
use App\Module\Restaurant\Storage\MenuStorageInterface;
use App\Storage\Entity\Reference;
use App\Storage\StorageInterface;
use Laminas\Hydrator\HydrationInterface;
use Laminas\Hydrator\HydratorInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

/**
 * Class RpcMenuController
 * @package App\Module\Restaurant\Controller
 */
class RpcMenuController implements RpcControllerInterface {

    /**
     * @var string
     */
    protected $hydratorService = 'RestMenuEntityWithResourceHydrator';

    /**
     * @var Twig
     */
    protected $twig;

    /**
     * var string
     */
    protected $jsPath;

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
                                Twig $twig,
                                ContainerInterface $container) {
        $this->twig = $twig;
        $this->jsPath = $container->get('settings')['twig']['path-js'];
        $this->menuStorage = $menuStorage;
        $this->organizationStorage = $organizationStorage;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {

        $slug = $request->getAttribute('__route__')->getArgument('slug');

        $resultSet = $this->organizationStorage->getAll(['normalize_name' => $slug]);
        // Restaurant not found
        if (!$resultSet->current()) {
            return $this->get404($response);
        }

        $menu = $this->menuStorage->getMenuByRestaurantSlug($slug);

        // Menu not found
        if (!$menu) {
            return $this->get404($response);
        }

        if (!$menu) {
            return $this->get404($response);
        }

        return $this->twig->render(
            $response,
            'index.html',
            [
                'base_url' => $this->jsPath,
                'menu' => $menu
            ]
        );
    }

    /**
     * @param Response $response
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function get404(Response $response) {
        return $this->twig->render(
            $response,
             '404.html',
            [
                'base_url' => $this->jsPath
            ]
        );
    }
}