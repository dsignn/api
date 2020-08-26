<?php
declare(strict_types=1);

namespace App\Module\Organization\Controller;

use App\Controller\RpcControllerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\Organization\Entity\OrganizationEntity;
use App\Module\Organization\Storage\OrganizationStorageInterface;
use App\Module\Resource\Storage\ResourceStorageInterface;
use App\Module\Restaurant\Entity\Embedded\MenuItem;
use App\Module\Restaurant\Entity\MenuEntity;
use App\Storage\Entity\Reference;
use App\Storage\StorageInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Laminas\InputFilter\InputFilterInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\UploadedFile;

/**
 * Class RpcUploadResourceOrganization
 * @package App\Module\Organization\Entity
 */
class RpcUploadResourceOrganization implements RpcControllerInterface {

    use AcceptServiceAwareTrait;

    /**
     * @var string
     */
    protected $hydratorService = 'RestOrganizationEntityHydrator';


    /**
     * @var StorageInterface
     */
    protected $organizationStorage;

    /**
     * @var StorageInterface
     */
    protected $resourceStorage;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * RpcUploadResourceOrganization constructor.
     * @param ResourceStorageInterface $resourceStorage
     * @param OrganizationStorageInterface $organizationStorage
     * @param Client $client
     * @param ContainerInterface $container
     */
    public function __construct(ResourceStorageInterface $resourceStorage, OrganizationStorageInterface $organizationStorage, Client $client, ContainerInterface $container) {
        $this->organizationStorage = $organizationStorage;
        $this->resourceStorage = $resourceStorage;
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

        /** @var OrganizationEntity $entity */
        $entity = $this->organizationStorage->get($data['organization_id']);
        if (!$entity) {
            return $response->withStatus(404);
        }

        try {
            $resourceResponse = $this->getRequest($entity, $data['file']->getStream()->getMetadata('uri'));
        } catch (ClientException $exception) {
            $streamFactory = new StreamFactory();
            return $response->withStatus($exception->getCode())->withBody($streamFactory->createStream(
                $exception->getResponse()->getBody()->getContents()
            ));
        }

        $resourceEntity = $this->resourceStorage->getEntityPrototype()->getPrototype($resourceResponse);
        $this->resourceStorage->getHydrator()->hydrate($resourceResponse, $resourceEntity);

        $entity->setLogo(new Reference($resourceEntity->getId(), 'resource'));
        $this->organizationStorage->update($entity);

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $entity);
    }

    /**
     * @param OrganizationEntity $entity
     * @param string $srcFile
     * @return mixed
     */
    protected function getRequest(OrganizationEntity $entity, string $srcFile) {
        $data = [
            //'debug' => true,
            'headers' => [
                'Accept' => 'application/json'
            ],

            'multipart' => [
                [
                    'name' => 'name',
                    'contents' => 'logo organization'
                ],

                [
                    'name'     => 'file',
                    'contents' => fopen($srcFile, 'r')
                ]

            ]

        ];

        $id = '';
        $method = 'post';
        if (!!$entity->getLogo()->getId()) {
            $id = '/' . $entity->getLogo()->getId();
            $method = 'patch';
        }

        $url = $this->url . '/resource' . $id;
        $response = $this->client->{strtolower($method)}($url, $data);

        return json_decode($response->getBody()->getContents(), true);
    }
}