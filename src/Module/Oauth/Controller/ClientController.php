<?php
declare(strict_types=1);

namespace App\Module\Oauth\Controller;

use App\Controller\RestController;
use App\Controller\RestControllerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use App\Module\Oauth\Storage\ClientStorageInterface;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\StreamFactory;

/**
 * Class ClientController
 * @package App\Module\Oauth\Controller
 */
class ClientController extends RestController implements RestControllerInterface {

    use AcceptServiceAwareTrait;

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