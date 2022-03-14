<?php
declare(strict_types=1);

namespace App\Module\Order\Controller;

use App\Controller\AllRpcController;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\Order\Storage\OrderStorageInterface;
use Psr\Container\ContainerInterface;

/**
 * Class AllRpcOrderController
 * @package App\Module\Organization\Controller
 */
class AllRpcOrderController extends AllRpcController {

    use AcceptServiceAwareTrait;

    /**
     * @var string
     */
    protected $hydratorService = 'RestOrderEntityHydrator';

    /**
     * AllRpcResourceController constructor.
     * @param OrderStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(OrderStorageInterface $storage, ContainerInterface $container) {
        parent::__construct($storage, $container);
    }
}