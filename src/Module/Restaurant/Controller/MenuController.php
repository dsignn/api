<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Controller;

use App\Controller\RestController;
use App\Controller\RestControllerInterface;
use App\Module\Restaurant\Storage\MenuStorageInterface;
use Psr\Container\ContainerInterface;

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
}