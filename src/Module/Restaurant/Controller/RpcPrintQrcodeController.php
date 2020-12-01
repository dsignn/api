<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Controller;

use App\Controller\RpcControllerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\Organization\Entity\OrganizationEntity;
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
 * Class RpcPrintQrcodeController
 * @package App\Module\Restaurant\Controller
 */
class RpcPrintQrcodeController implements RpcControllerInterface {

    /**
     * @var string
     */
    protected $hydratorService = 'RestOrganizationEntityHydrator';

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
    protected $resourceStorage;

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
     * @param OrganizationStorageInterface $organizationStorage
     * @param Twig $twig
     * @param ContainerInterface $container
     */
    public function __construct(
        ResourceStorageInterface $resourceStorage,
        OrganizationStorageInterface $organizationStorage,
        Twig $twig,
        ContainerInterface $container) {

        $this->twig = $twig;
        $this->jsPath = $container->get('settings')['twig']['path-js'];
        $this->organizationStorage = $organizationStorage;
        $this->resourceStorage = $resourceStorage;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {

        $id = $request->getAttribute('__route__')->getArgument('id');

        /** @var OrganizationEntity $organization */
        $organization = $this->organizationStorage->get($id);


        // Restaurant not found
        if (!$organization) {
            return $this->get404($response);
        }

        // Logo not found
        if (!$organization->getQrCode()->getId() || $organization->getQrCode()->getId() === '') {
            // TODO personalize
            return $this->get404($response);
        }

        $resource = $this->resourceStorage->get($organization->getQrCode()->getId());

        return $this->twig->render(
            $response,
            'print-qrcode-index.html',
            [
                'base_url' => $this->jsPath,
                'resource'=> $resource
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