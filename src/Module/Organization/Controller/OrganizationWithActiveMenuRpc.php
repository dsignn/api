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
use MongoDB\BSON\ObjectId;

/**
 * Class OrganizationWithActiveMenuRpc
 * @package App\Module\Organization\Controller
 */
class OrganizationWithActiveMenuRpc implements RpcControllerInterface
{

    use AcceptServiceAwareTrait;

    /**
     * @var string
     */
    protected $hydratorService = 'RestOrganizationEntityHydrator';

    /**
     * @var OrganizationStorageInterface
     */
    protected $storage;


    /**
     * @inheritDoc
     */
    public function __construct(OrganizationStorageInterface $storage, ContainerInterface $container) {
        $this->storage = $storage;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {

        $organizationMenu = $this->extract($this->storage->getRandomRestaurantMenu());


        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $organizationMenu);
    }

    /**
     * TODO refactor and introduce the resulset in storage and the property hydrator
     *
     * @return void
     */
    protected function extract(array $organizationMenu) {

        for ($cont = 0; count($organizationMenu) > $cont; $cont++) {
            if (isset($organizationMenu[$cont]['_id']) && $organizationMenu[$cont]['_id'] instanceof ObjectId) {
                $organizationMenu[$cont]['id'] = $organizationMenu[$cont]['_id']->__toString();
                unset($organizationMenu[$cont]['_id']);
            }

            if (isset($organizationMenu[$cont]['qr_code']) && $organizationMenu[$cont]['qr_code']->id instanceof ObjectId) {
                $organizationMenu[$cont]['qr_code']->id = $organizationMenu[$cont]['qr_code']->id->__toString();
            }

            if (isset($organizationMenu[$cont]['qr_code_delivery']) && $organizationMenu[$cont]['qr_code_delivery']->id instanceof ObjectId) {
                $organizationMenu[$cont]['qr_code']->id = $organizationMenu[$cont]['qr_code']->id->__toString();
            }

            if (isset($organizationMenu[$cont]['menus']) && is_array($organizationMenu[$cont]['menus'])) {
                
                $menus = $organizationMenu[$cont]['menus'];
                for ($cont2 = 0;  count($menus) > $cont2; $cont2++) {
                   
                    unset($menus[$cont2]->organization);

                    if (isset($menus[$cont2]->_id) && $menus[$cont2]->_id instanceof ObjectId) {
                        $menus[$cont2]->id = $menus[$cont2]->_id->__toString();
                        unset($menus[$cont2]->_id);
                    }

                    if (isset($menus[$cont2]->items) && is_array($menus[$cont2]->items)) {
                       
                        for ($cont3 = 0;  count($menus[$cont2]->items) > $cont3; $cont3++) { 
                            $menu = $menus[$cont2]->items[$cont3];
                            if (isset($menu->_id) &&  $menu->_id instanceof ObjectId) {
                                $menu->id = $menu->_id->__toString();
                                unset($menu->_id);
                            }

                            if (isset($menu->_id) &&  is_string($menu->_id)) {
                                $menu->id = $menu->_id;
                                unset($menu->_id);
                            }

                            if(isset($menu->photos) && is_array($menu->photos)) {
                                for ($cont4 = 0; count($menu->photos) > $cont4; $cont4++) {
                                    if (isset($menu->photos[$cont4]->_id) &&  $menu->photos[$cont4]->_id instanceof ObjectId) {
                                        $menu->photos[$cont4]->id = $menu->photos[$cont4]->_id->__toString();
                                        unset($menu->photos[$cont4]->_id);
                                    }

                                    if (isset($menu->photos[$cont4]->_id) &&  is_string($menu->photos[$cont4]->_id)) {
                                        $menu->photos[$cont4]->id = $menu->photos[$cont4]->_id;
                                        unset($menu->photos[$cont4]->_id);
                                    }
                                }
                            }
                        }
                    }        
                }
            }
        }
        return $organizationMenu;
    }
}