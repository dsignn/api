<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Controller;

use App\Controller\RpcControllerInterface;
use App\Module\Organization\Entity\OrganizationEntity;
use App\Module\Organization\Storage\OrganizationStorageInterface;
use App\Module\Resource\Storage\ResourceStorageInterface;
use App\Module\Restaurant\Storage\MenuCategoryStorageInterface;
use App\Module\Restaurant\Storage\MenuStorage;
use App\Module\Restaurant\Storage\MenuStorageInterface;
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
     * @param MenuCategoryStorageInterface $menuCategoryStorage
     * @param Twig $twig
     * @param ContainerInterface $container
     */
    public function __construct(
        MenuStorageInterface $menuStorage,
        OrganizationStorageInterface $organizationStorage,
        MenuCategoryStorageInterface $menuCategoryStorage,
        Twig $twig,
        ContainerInterface $container) {

        $this->twig = $twig;
        $this->jsPath = $container->get('settings')['twig']['path-js'];
        $this->rootPath = $container->get('settings')['twig']['rootPath'];
        $this->menuStorage = $menuStorage;
        $this->organizationStorage = $organizationStorage;
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
        $menu = $this->menuStorage->getAll($arraySearch)->current();

        // Restaurant not found
        if (!$menu) {
            return $this->get404($response);
        }

        $organization = $this->organizationStorage->get($menu->getOrganization()->getId());   
     
        $this->twig->getEnvironment()->addFilter($this->getTwigCategoryFilter());
        $this->twig->getEnvironment()->addFilter($this->getTwigCurrencyFilter());
        $this->twig->getEnvironment()->addFilter($this->getTwigLanguageFilter());
        $this->twig->getEnvironment()->addFilter($this->getTwigNumerFilter());

        $hasEnglish = false;
        if (count($menu->getItems()) > 0) {
            try {
 
                $hasEnglish = isset($menu->getItems()[0]->getName()['en']);
            } catch(\Exception $e) {
                // TODO write to log
            }
        }

        return $this->twig->render(
            $response,
            'print-menu-default.html',
            [
                'base_url' => $this->jsPath,
                'organization'=> $organization,
                'menu' => $menu,
                'hasEnglish' => $hasEnglish
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
    }

    /**
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

    /**
     * @return TwigFilter
     */
    protected function getTwigCurrencyFilter() {
        return new TwigFilter(
            'currency',
            function($value) {

                switch($value) {
                    case 'EUR':
                        return '€';
                }

                return $value;
            }
        );
    }

    /**
     * @return TwigFilter
     */
    protected function getTwigLanguageFilter() {
        return new TwigFilter(
            'lang',
            function($value, $lang) {

                if (isset($value[$lang])) {
                    return $value[$lang];
                }
            }
        );
    }


    /**
     * @return TwigFilter
     */
    protected function getTwigNumerFilter() {
        return new TwigFilter(
            'number',
            function($value, $lang) {

               
                switch($lang) {
                    case 'en':
                    case 'it':
                        return number_format($value, 2, '.', '');
                }

                return $value;
            }
        );
    }
}