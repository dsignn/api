<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Controller;

use App\Controller\RestController;
use App\Controller\RestControllerInterface;
use App\Module\Restaurant\Storage\MenuStorageInterface;
use Laminas\InputFilter\InputFilterInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function DI\value;

/**
 * Class MenuController
 * @package App\Module\Restaurant\Controller
 */
class MenuController extends RestController implements RestControllerInterface {

    /**
     * @var string
     */
    protected $hydratorService = 'RestMenuEntityHydrator';

    /**
     * @inheritDoc
     */
    public function __construct(MenuStorageInterface $storage, ContainerInterface $container) {
        parent::__construct($storage, $container);
    }

    public function paginate(Request $request, Response $response)
    {
        $filter = $request->getAttribute('app-data-filter');
        $query =  array_merge($filter ? $filter : [], $request->getQueryParams());

        $page = isset($query['page']) ? intval($query['page']) ? intval($query['page']) : 1 : 1;
        unset($query['page']);
        $itemPerPage = isset($query['item-per-page']) ? intval($query['item-per-page']) ? intval($query['item-per-page']) : 10 : 10;
        unset($query['item-per-page']);


        $pagination = $this->storage->getPage($page, $itemPerPage, $query);

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $pagination);
    }
}