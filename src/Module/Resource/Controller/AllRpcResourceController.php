<?php
declare(strict_types=1);

namespace App\Module\Resource\Controller;

use App\Controller\AllRpcController;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\Resource\Storage\ResourceStorageInterface;
use Psr\Container\ContainerInterface;

/**
 * Class AllRpcResourceController
 * @package App\Module\Resource\Controller
 */
class AllRpcResourceController extends AllRpcController {

    use AcceptServiceAwareTrait;

    /**
     * @var string
     */
    protected $hydratorService = 'RestResourceEntityHydrator';

    /**
     * AllRpcResourceController constructor.
     * @param ResourceStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(ResourceStorageInterface $storage, ContainerInterface $container) {
       parent::__construct($storage, $container);
    }
}