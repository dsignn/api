<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Controller;

use App\Controller\RpcControllerInterface;
use App\Module\Organization\Entity\Embedded\Address\Address;
use App\Module\Organization\Entity\Embedded\Phone\Phone;
use App\Module\Organization\Entity\OrganizationEntity;
use App\Module\Organization\Storage\OrganizationStorageInterface;
use App\Module\Resource\Storage\ResourceStorageInterface;
use App\Module\Restaurant\Entity\MenuEntity;
use App\Module\Restaurant\Storage\MenuCategoryStorageInterface;
use App\Module\Restaurant\Storage\MenuStorage;
use App\Module\Restaurant\Storage\MenuStorageInterface;
use App\Module\Restaurant\Twig\Filter\AddressFilter;
use App\Module\Restaurant\Twig\Filter\CurrencyFilter;
use App\Module\Restaurant\Twig\Filter\LanguageFilter;
use App\Module\Restaurant\Twig\Filter\NumberFilter;
use App\Module\Restaurant\Twig\Filter\PhoneFilter;
use App\Storage\StorageInterface;
use NumberFormatter;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\TwigFilter;

/**
 * Class RpcPrintQrcodeController
 * @package App\Module\Restaurant\Controller
 */
class RpcPrintMenuController implements RpcControllerInterface {

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
     * var string
     */
    protected $rootPath;

    /**
     * @var StorageInterface
     */
    protected $menuStorage;

    /**
     * @var StorageInterface
     */
    protected $organizationStorage;

    /**
     * @var StorageInterface
     */
    protected $menuCategoryStorage;

    /**
     * @var StorageInterface
     */
    protected $resourceStorage;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var [type]
     */
    protected $categories;

    /**
     * RpcMenuController constructor.
     * @param MenuStorageInterface $organizationStorage
     * @param OrganizationStorageInterface $organizationStorage
     * @param ResourceStorageInterface $resourceStorage
     * @param MenuCategoryStorageInterface $menuCategoryStorage
     * @param Twig $twig
     * @param ContainerInterface $container
     */
    public function __construct(
        MenuStorageInterface $menuStorage,
        OrganizationStorageInterface $organizationStorage,
        ResourceStorageInterface $resourceStorage,
        MenuCategoryStorageInterface $menuCategoryStorage,
        Twig $twig,
        ContainerInterface $container) {

        $this->twig = $twig;
        $this->jsPath = $container->get('settings')['twig']['path-js'];
        $this->rootPath = $container->get('settings')['twig']['rootPath'];
        $this->menuStorage = $menuStorage;
        $this->organizationStorage = $organizationStorage;
        $this->resourceStorage = $resourceStorage;
        $this->menuCategoryStorage = $menuCategoryStorage;
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {

        $id = $request->getAttribute('__route__')->getArgument('id');

        /** @var App\Module\Restaurant\Entity\MenuEntity $menu */

        $arraySearch = array_merge(['id'  => $id], $request->getAttribute('app-data-filter'));
        /** @var MenuEntity  $menu */
        $menu = $this->menuStorage->getAll($arraySearch)->current();

        // Restaurant not found
        if (!$menu) {
            return $this->get404($response);
        }

        /** @var OrganizationEntity  $organization */
        $organization = $this->organizationStorage->get($menu->getOrganization()->getId());   
     
        $this->twig->getEnvironment()->addFilter($this->getTwigCategoryFilter());
        $this->twig->getEnvironment()->addFilter(CurrencyFilter::getFilter());
        $this->twig->getEnvironment()->addFilter(LanguageFilter::getFilter());
        $this->twig->getEnvironment()->addFilter(NumberFilter::getFilter());
        $this->twig->getEnvironment()->addFilter(AddressFilter::getFilter());
        $this->twig->getEnvironment()->addFilter(PhoneFilter::getFilter());

        $hasEnglish = false;
        if (count($menu->getItems()) > 0) {
            try {
 
                $hasEnglish = isset($menu->getItems()[0]->getName()['en']);
            } catch(\Exception $e) {
                // TODO write to log
            }
        }

        // Retrive qrcode
        $qrcode = null;
        switch($menu->getStatus()) {
            case MenuEntity::$STATUS_ENABLE:
                $qrcode = $this->resourceStorage->get($organization->getQrCode()->getId());
                break;
            case MenuEntity::$STATUS_DELIVERY:
                $qrcode = $this->resourceStorage->get($organization->getQrCodeDelivery()->getId());
                break;
        }

          // Retrive logo
        $logo = $organization->getLogo()->getId() ? 
            $this->resourceStorage->get($organization->getLogo()->getId()) : null;

        return $this->twig->render(
            $response,
            'print-menu-default.html',
            [
                'base_url' => $this->jsPath,
                'organization'=> $organization,
                'menu' => $menu,
                'hasEnglish' => $hasEnglish,
                'qrcode' => $qrcode,
                'logo' => $logo
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
             'restaurant-404.html',
            [
                'base_url' => $this->jsPath
            ]
        );
    }    /**
    * @return void
    */
   protected function getCategories() {
       if (!$this->categories) {
           $this->categories = $this->menuCategoryStorage->getAll([])->current();
       }

       return $this->categories;
   }


    /**
     * @return TwigFilter
     */
    protected function getTwigCategoryFilter() {
        return new TwigFilter(
            'category',
            function($value, $lang) {

                /** @var mixed */
                $categoryMenuEntity = $this->getCategories();
                $cont = 0;
                while ($cont < count($categoryMenuEntity->plates)) {
                    if ($value === $categoryMenuEntity->plates[$cont]['name']) {
                        return  $categoryMenuEntity->plates[$cont]['translation'][$lang];
                    }
                    $cont++;
                }

                return $value;
            }
        );
    }
}