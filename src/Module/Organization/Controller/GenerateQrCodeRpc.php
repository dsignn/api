<?php
declare(strict_types=1);

namespace App\Module\Organization\Controller;

use App\Controller\RpcControllerInterface;
use App\Crypto\CryptoInterface;
use App\Hydrator\Strategy\HydratorStrategy;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\Organization\Entity\OrganizationEntity;
use App\Module\Organization\Storage\OrganizationStorageInterface;
use App\Module\Resource\Entity\ImageResourceEntity;
use App\Module\User\Mail\UserMailerInterface;
use App\Module\User\Storage\UserStorageInterface;
use App\Storage\Entity\Reference;
use App\Storage\Entity\SingleEntityPrototype;
use App\Storage\StorageInterface;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use GuzzleHttp\Client;
use Laminas\Hydrator\ClassMethodsHydrator;
use mysql_xdevapi\Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpException;

/**
 * Class GenerateQrCodeRpc
 * @package App\Module\Organization\Controller
 */
class GenerateQrCodeRpc implements RpcControllerInterface {

    use AcceptServiceAwareTrait;

    /**
     * @var string
     */
    protected $hydratorService = 'RestOrganizationEntityHydrator';

    /*
     * @var string
     */
    protected $tmp;

    /**
     * @var
     */
    protected $url;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @inheritDoc
     */
    public function __construct(OrganizationStorageInterface $storage, Client $client, ContainerInterface $container) {

        $this->storage = $storage;
        $this->client = $client;
        $this->tmp = $container->get('settings')['tmp'];
        $this->url = $container->get('settings')['httpClient']["url"];
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {

        $id = $request->getAttribute('__route__')->getArgument('id');
        /** @var OrganizationEntity $entity */
        $entity = $this->storage->get($id);
        if (!$entity) {
            return $response->withStatus(404);
        }

        $tmpFile = $this->generateQrCode($entity);
        $method = $entity->getQrCode()->getId() ? 'patch' : 'post';

        try {
           /** @var \GuzzleHttp\Psr7\Response $responseResource */
           $responseResource = $this->getRequest($entity, $method, $tmpFile);

       } catch (\Exception $e){
            throw new HttpException($request, 'Qr code generator error',500, $e);
       }

        $entity->getQrCode()->setId($responseResource->id);
        $entity->getQrCode()->setCollection('resource');
        $this->storage->update($entity);

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $entity);
    }

    /**
     * @param OrganizationEntity $entity
     * @param string $method
     * @param string $tmpFile
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getRequest(OrganizationEntity $entity, string $method, string $tmpFile) {
        $data = [
            //'debug' => true,
            'headers' => [
                'Accept' => 'application/json'
            ],

            'multipart' => [
                [
                    'name' => 'name',
                    'contents' => 'qrcode'
                ],
                [
                    'name'     => 'file',
                    'contents' => fopen($tmpFile, 'r')
                ]
            ]

        ];


        $url = $this->url . ($entity->getQrCode()->getId() ? '/resource/' .$entity->getQrCode()->getId() : '/resource');
        $response = $this->client->{$method}($url, $data);

        return json_decode($response->getBody()->getContents());
    }


    /**
     * @param OrganizationEntity $entity
     * @return string
     * @throws \Exception
     */
    protected function generateQrCode(OrganizationEntity $entity) {

        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new ImagickImageBackEnd()
        );
        $writer = new Writer($renderer);
        // TODO url from config

        $string =  $renderer->render(Encoder::encode($this->url . '/' . $entity->getNormalizeName(),  ErrorCorrectionLevel::L()));

        $path = $this->tmp . "/" . uniqid() . '.png';
        $im = imagecreatefromstring($string);
        $resp = imagepng($im, $path);
        imagedestroy($im);

        return $path;
    }
}