<?php
declare(strict_types=1);

namespace App\Module\Device\Controller;

use App\Controller\RestController;
use App\Controller\RestControllerInterface;
use App\Module\Device\Storage\DeviceStorageInterface;
use Psr\Container\ContainerInterface;

/**
 * Class DeviceController
 * @package App\Module\Device\Controller
 */
class DeviceController extends RestController implements RestControllerInterface {

    /**
     * MonitorController constructor.
     * @param DeviceStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(DeviceStorageInterface $storage, ContainerInterface $container) {
        parent::__construct($storage, $container);
    }
}