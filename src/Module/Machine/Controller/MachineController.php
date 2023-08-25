<?php
declare(strict_types=1);

namespace App\Module\Machine\Controller;

use App\Controller\RestController;
use App\Controller\RestControllerInterface;
use App\Module\Machine\Storage\MachineStorageInterface;
use Psr\Container\ContainerInterface;

/**
 * Class MachineController
 * @package App\Module\Machine\Controller
 */
class MachineController extends RestController implements RestControllerInterface {
    /**
     * @var string
     */
    protected $hydratorService = 'RestMachineEntityHydrator';

    /**
     * MonitorController constructor.
     * @param MonitorStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(MachineStorageInterface $storage, ContainerInterface $container) {
        parent::__construct($storage, $container);
    }
}