<?php
declare(strict_types=1);

namespace App\Module\Monitor\Controller;

use App\Controller\RestController;
use App\Controller\RestControllerInterface;
use App\Module\Monitor\Storage\MonitorStorageInterface;
use Psr\Container\ContainerInterface;

/**
 * Class MonitorController
 * @package App\Module\Monitor\Controller
 */
class MonitorController extends RestController implements RestControllerInterface {
    /**
     * @var string
     */
    protected $hydratorService = 'RestMonitorEntityHydrator';

    /**
     * MonitorController constructor.
     * @param MonitorStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(MonitorStorageInterface $storage, ContainerInterface $container) {
        parent::__construct($storage, $container);
    }
}