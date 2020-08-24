<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Controller;

use App\Controller\RpcControllerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\Organization\Entity\OrganizationEntity;
use App\Module\Resource\Storage\ResourceStorageInterface;
use App\Module\Restaurant\Entity\Embedded\MenuItem;
use App\Module\Restaurant\Entity\MenuEntity;
use App\Module\Restaurant\Storage\MenuStorageInterface;
use App\Storage\Entity\Reference;
use App\Storage\StorageInterface;
use Aws\Exception\AwsException;use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use Laminas\InputFilter\InputFilterInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\UploadedFile;

/**
 * Class RpcUploadResourceMenuItem
 * @package App\Module\Restaurant\Controller
 */
class RpcUploadResourceMenuItem implements RpcControllerInterface {

    use AcceptServiceAwareTrait;

    /**
     * @var string
     */
    protected $hydratorService = 'RestMenuEntityHydrator';

    /**
     * @var StorageInterface
     */
    protected $resourceStorage;

    /**
     * @var StorageInterface
     */
    protected $menuStorage;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var
     */
    protected $container;

    /**
     * RpcUploadResourceMenuItem constructor.
     * @param ResourceStorageInterface $resourceStorage
     * @param MenuStorageInterface $menuStorage
     * @param Client $client
     * @param ContainerInterface $container
     */
    public function __construct(ResourceStorageInterface $resourceStorage, MenuStorageInterface $menuStorage, Client $client, ContainerInterface $container) {
        $this->resourceStorage = $resourceStorage;
        $this->menuStorage = $menuStorage;
        $this->url = $container->get('settings')['httpClient']["url"];
        $this->client = $client;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {

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

        /** @var MenuEntity $entity */
        $entity = $this->menuStorage->get($data['menu_id']);
        if (!$entity) {
            return $response->withStatus(404);
        }


        $notFoundMenuItem = true;
        /** @var MenuItem $item */
        foreach ($entity->getItems() as $item) {
            if ($item->getId() === $data['resource_menu_id']) {
                $menuItem = $item;
                $notFoundMenuItem = false;
                break;
            }
        }

        if ($notFoundMenuItem) {
            return $response->withStatus(404);
        }

        $resourceResponse = $this->getRequest($menuItem, $data['file']->getStream()->getMetadata('uri'));

        $resourceEntity = $this->resourceStorage->getEntityPrototype()->getPrototype($resourceResponse);
        $this->resourceStorage->getHydrator()->hydrate($resourceResponse, $resourceEntity);
        $menuItem->setPhotos([new Reference($resourceEntity->getId(), 'resource')]);
        $this->menuStorage->update($entity);

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $entity);
    }

    /**
     * @param MenuItem $menuItem
     * @param string $method
     * @param string $srcFile
     * @return mixed
     */
    protected function getRequest(MenuItem $menuItem, string $srcFile) {
        $data = [
            //'debug' => true,
            'headers' => [
                'Accept' => 'application/json'
            ],

            'multipart' => [
                [
                    'name' => 'name',
                    'contents' => 'photo menù'
                ],

                [
                    'name'     => 'file',
                    'contents' => fopen($srcFile, 'r')
                ]

            ]

        ];

        $id = '';
        $method = 'POST';
        if (count($menuItem->getPhotos()) > 0) {
            $id = '/' . $menuItem->getPhotos()[0]->getId();
        }

        /** @var UploadedFile $file */
        $method = count($menuItem->getPhotos()) === 0 ? 'POST' : 'PATCH';
        $url = $this->url . '/resource' . $id;

        $response = $this->client->{strtolower($method)}($url, $data);

        return json_decode($response->getBody()->getContents(), true);
    }
}