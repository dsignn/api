<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Controller;

use App\Controller\RpcControllerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\Resource\Storage\ResourceStorageInterface;
use App\Module\Restaurant\Entity\Embedded\MenuItem;
use App\Module\Restaurant\Entity\MenuEntity;
use App\Module\Restaurant\Storage\MenuStorageInterface;
use App\Storage\StorageInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Laminas\InputFilter\InputFilterInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\StreamFactory;

/**
 * Class RpcDeleteResourceMenuItem
 * @package App\Module\Restaurant\Controller
 */
class RpcDeleteResourceMenuItem implements RpcControllerInterface {

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

        $data = array_merge($request->getParsedBody());

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

        switch (true) {
            case $notFoundMenuItem === true;
            case count($menuItem->getPhotos()) < 1;
                return $response->withStatus(404);
                break;
        }


        try {
            $resourceResponse = $this->getRequest($menuItem);
        } catch (ClientException $exception) {
            $streamFactory = new StreamFactory();
            return $response->withStatus($exception->getCode())->withBody($streamFactory->createStream(
                $exception->getResponse()->getBody()->getContents()
            ));
        }

        $menuItem->setPhotos([]);
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
    protected function getRequest(MenuItem $menuItem) {
        $data = [
            //'debug' => true,
            'headers' => [
                'Accept' => 'application/json'
            ]
        ];

        $method = 'delete';
        $url = $this->url . '/resource/' . $menuItem->getPhotos()[0]->getId();

        $response = $this->client->{$method}($url, $data);

        return json_decode($response->getBody()->getContents(), true);
    }
}