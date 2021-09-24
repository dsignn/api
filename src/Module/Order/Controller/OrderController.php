<?php
declare(strict_types=1);

namespace App\Module\Order\Controller;

use App\Controller\RestController;
use App\Controller\RestControllerInterface;
use App\Module\Order\Storage\OrderStorageInterface;
use Psr\Container\ContainerInterface;

/**
 * Class OrderController
 * @package App\Module\Order\Controller
 */
class OrderController extends RestController implements RestControllerInterface {

    /**
     * @var string
     */
    protected $hydratorService = 'RestOrganizationEntityHydrator';

    /**
     * @inheritDoc
     */
    public function __construct(OrderStorageInterface $storage, ContainerInterface $container) {
        parent::__construct($storage, $container);
    }
}