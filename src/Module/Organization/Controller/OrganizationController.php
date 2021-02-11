<?php
declare(strict_types=1);

namespace App\Module\Organization\Controller;

use App\Controller\RestController;
use App\Controller\RestControllerInterface;
use App\Module\Organization\Storage\OrganizationStorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class OrganizationController
 * @package App\Module\Organization\Controller
 */
class OrganizationController extends RestController implements RestControllerInterface {

    /**
     * @var string
     */
    protected $hydratorService = 'RestOrganizationEntityHydrator';

    /**
     * @inheritDoc
     */
    public function __construct(OrganizationStorageInterface $storage, ContainerInterface $container) {
        parent::__construct($storage, $container);
    }

    public function paginate(Request $request, Response $response)
    {

        return parent::paginate($request, $response); // TODO: Change the autogenerated stub
    }


}