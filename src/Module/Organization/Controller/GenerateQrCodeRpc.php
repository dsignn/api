<?php
declare(strict_types=1);

namespace App\Module\Organization\Controller;

use App\Controller\RpcControllerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\Organization\Entity\OrganizationEntity;
use App\Module\Organization\Storage\OrganizationStorageInterface;
use App\Storage\StorageInterface;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpException;

/**
 * Class GenerateQrCodeRpc
 * @package App\Module\Organization\Controller
 */
class GenerateQrCodeRpc implements RpcControllerInterface
{

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
     * @var
     */
    protected $urlMenu;

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
    public function __construct(OrganizationStorageInterface $storage, Client $client, ContainerInterface $container)
    {

        $this->storage = $storage;
        $this->client = $client;
        $this->tmp = $container->get('settings')['tmp'];
        $this->url = $container->get('settings')['httpClient']["url"];
        $this->urlMenu = $container->get('settings')['urlMenu'];
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response)
    {

        $id = $request->getAttribute('__route__')->getArgument('id');

        /** @var OrganizationEntity $entity */
        $entity = $this->storage->get($id);
        if (!$entity) {
            return $response->withStatus(404);
        }

        $queryParam = '';
        switch (true) {
            case isset($request->getQueryParams()['delivery']) === true:
                $queryParam = '?delivery';
                break;
        }

        $tmpFile = $this->generateQrCode($entity, $queryParam);


        try {
            /** @var \GuzzleHttp\Psr7\Response $responseResource */
            $responseResource = $this->getRequest($entity, $queryParam, $tmpFile);

        } catch (\Exception $e) {
            throw new HttpException($request, 'Qr code generator error', 500, $e);
        }

        if (!$queryParam) {
            $entity->getQrCode()->setId($responseResource->id);
            $entity->getQrCode()->setCollection('resource');
        } else {
            $entity->getQrCodeDelivery()->setId($responseResource->id);
            $entity->getQrCodeDelivery()->setCollection('resource');
        }

        $this->storage->update($entity);

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $entity);
    }

    /**
     * @param OrganizationEntity $entity
     * @param string $queryParam
     * @param string $tmpFile
     * @return mixed
     */
    protected function getRequest(OrganizationEntity $entity, string $queryParam, string $tmpFile)
    {
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
                    'name' => 'file',
                    'contents' => fopen($tmpFile, 'r')
                ]
            ]

        ];

        switch (true) {
            case !$queryParam === true:
                $method = $entity->getQrCode()->getId() ? 'patch' : 'post';
                $url = $this->url . ($entity->getQrCode()->getId() ? '/resource/' . $entity->getQrCode()->getId() : '/resource');
                break;
            case !$queryParam === false:
                $method = $entity->getQrCodeDelivery()->getId() ? 'patch' : 'post';
                $url = $this->url . ($entity->getQrCodeDelivery()->getId() ? '/resource/' . $entity->getQrCodeDelivery()->getId() : '/resource');
                break;
        }

        $response = $this->client->{$method}($url, $data);
        return json_decode($response->getBody()->getContents());
    }


    /**
     * @param OrganizationEntity $entity
     * @param string $urlParam
     * @return string
     */
    protected function generateQrCode(OrganizationEntity $entity, string $queryParam = '')
    {
        $pathLogo = __DIR__ . '/../../../../asset/logo_bordo.png';

        $qrCode = new \Endroid\QrCode\QrCode($this->urlMenu . '/' . $entity->getNormalizeName() . $queryParam);
        $qrCode->setSize(300);
        $qrCode->setMargin(10);
        $qrCode->setWriterByName('png');
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::HIGH());
        $qrCode->setForegroundColor(['r' => 1, 'g' => 91, 'b' => 96, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        $qrCode->setLogoPath($pathLogo);
        $qrCode->setLogoSize(100);
        $qrCode->setValidateResult(false);

        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new ImagickImageBackEnd()
        );

        $writer = new Writer($renderer);
        // TODO url from config


        $path = $this->tmp . "/" . uniqid() . '.png';
        $im = imagecreatefromstring($qrCode->writeString());
        $resp = imagepng($im, $path);
        imagedestroy($im);

        return $path;
    }
}