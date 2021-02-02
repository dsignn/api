<?php
declare(strict_types=1);

namespace App\Module\Restaurant\Controller;

use App\Controller\RpcControllerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class RpcFrontendRedirectController
 * @package App\Module\Restaurant\Controller
 */
class RpcFrontendRedirectController implements RpcControllerInterface {

    protected $urlRedirect;

    /**
     * RpcFrontendRedirectController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->urlRedirect = $container->get('settings')['urlFrontend'];
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {
        // TODO: Implement rpc() method.
        return $response->withHeader('Location', $this->urlRedirect)->withStatus(302);
    }
}