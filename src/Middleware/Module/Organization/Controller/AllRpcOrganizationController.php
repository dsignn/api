<?php
declare(strict_types=1);

namespace App\Module\Organization\Controller;

use App\Controller\AllRpcController;
use App\Module\Organization\Storage\OrganizationStorageInterface;
use Psr\Container\ContainerInterface;

/**
 * Class AllRpcOrganizationController
 * @package App\Module\Organization\Controller
 */
class AllRpcOrganizationController extends AllRpcController {

    /**
     * @var string
     */
    protected $hydratorService = 'RestOrganizationEntityHydrator';

    /**
     * AllRpcResourceController constructor.
     * @param OrganizationStorageInterface $storage
     * @param ContainerInterface $container
     */
    public function __construct(OrganizationStorageInterface $storage, ContainerInterface $container) {
        parent::__construct($storage, $container);
    }
}