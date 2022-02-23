<?php
declare(strict_types=1);

namespace App\Module\Order\Middleware;

use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Middleware\CorsMiddleware;
use App\Storage\StorageInterface;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseSlim;

class CorsOrderAuthentication extends AuthenticationMiddleware {

    protected $origin;

    public function __construct(ResourceServer $server, StorageInterface $userStorage, StorageInterface $tokenStorage, StorageInterface $clientStorage, array $settings = []) {
        $this->server = $server;
        $this->userStorage = $userStorage;
        $this->tokenStorage = $tokenStorage;
        $this->clientStorage = $clientStorage;
        $this->settings = $settings;

        if ($settings['origin']) {
            $this->origin = $settings['origin'];
        }
    }

    /**
     * @inheritDoc
     */
    public function process(Request $request, RequestHandler $handler): Response {
    
        if (CorsMiddleware::isXhr($request)) {
            return $handler->handle($request);   
        }
        return parent::process($request, $handler);
    }

    protected function toSkip(Request $request) {
        return str_contains(CorsMiddleware::getCorsRequestHeader($request), $this->origin);
    }
}
