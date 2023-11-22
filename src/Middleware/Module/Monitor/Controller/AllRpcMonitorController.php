<?php
declare(strict_types=1);

namespace App\Module\Monitor\Controller;

use App\Controller\AllRpcController;
use App\Module\Monitor\Storage\MonitorStorageInterface;
use App\Module\Resource\Storage\ResourceStorageInterface;
use Psr\Container\ContainerInterface;

/**
 * Class AllRpcMonitorController
 * @package App\Module\Monitor\Controller
 */
class AllRpcMonitorController extends AllRpcController {

    /**
     * @var string
     */
    protected $hydratorService = 'RestResourceEntityHydrator';

    /**
     * AllRpcResourceController constructor.
     * @param MonitorStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(MonitorStorageInterface $storage, ContainerInterface $container) {
       parent::__construct($storage, $container);
    }
}