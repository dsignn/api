<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Controller;

use App\Controller\AllRpcController;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\Resource\Storage\ResourceStorageInterface;
use App\Module\Restaurant\Storage\MenuStorageInterface;
use Psr\Container\ContainerInterface;

/**
 * Class AllRpcMenuController
 * @package App\Module\Restaurant\Controller
 */
class AllRpcMenuController extends AllRpcController {

    use AcceptServiceAwareTrait;

    /**
     * @var string
     */
    protected $hydratorService = 'RestMenuEntityHydrator';

    /**
     * AllRpcResourceController constructor.
     * @param ResourceStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(MenuStorageInterface $storage, ContainerInterface $container) {
        parent::__construct($storage, $container);
    }

}