<?php
declare(strict_types=1);

namespace App\Module\Order\Middleware;

use App\Middleware\CorsMiddleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseSlim;

class CorsOrderAuthentication extends CorsMiddleware {

    protected $origin;

    /**
     * @param string $origin
     */
    public function __construct(string $origin) {
        $this->origin = $origin;
    }

    /**
     * @inheritDoc
     */
    public function process(Request $request, RequestHandler $handler): Response {
    
        if (CorsMiddleware::isXhr($request)) {
            $skip = str_contains($request->getHeaderLine(CorsMiddleware::$ORIGIN_HEADER), $this->origin);
            $response = $handler->handle($request->withAttribute('app-skip-auth', $skip));
            $response->withHeader('TEST ORIGIN GIVEN', $request->getHeaderLine(CorsMiddleware::$ORIGIN_HEADER));
            $response->withHeader('TEST ORIGIN INTERNAL', $skip ? 'si' : 'no');
            
            return $response;
        }
    }
}
