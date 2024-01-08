<?php
declare(strict_types=1);

namespace App\Module\Oauth\Controller;

use App\Controller\RestController;
use App\Controller\RestControllerInterface;
use App\Module\Oauth\Storage\ClientStorageInterface;
use Psr\Container\ContainerInterface;

/**
 * Class ClientController
 * @package App\Module\Oauth\Controller
 */
class ClientController extends RestController implements RestControllerInterface {

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @inheritDoc
     */
    public function __construct(ClientStorageInterface $storage, ContainerInterface $container) {
        parent::__construct($storage, $container);
    }
}