<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseSlim;

/**
 * Class CorsMiddleware
 * @package App\Middleware
 */
class CorsMiddleware implements Middleware
{
    public static $ORIGIN_HEADER = 'Origin';

    /**
     * @var bool
     */
    protected $isWildCard = true;

    /**
     * @var array
     */
    protected $enableHost = [];

    /**
     * @inheritDoc
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $header = $request->getHeaderLine(CorsMiddleware::$ORIGIN_HEADER);

        if (CorsMiddleware::isXhr($request)) {
            switch (true) {
                case $this->isWildCard === true:
                    return CorsMiddleware::addCorsHeader($handler->handle($request),'*', ['PUT', 'DELETE', 'POST', 'GET', 'PATCH', 'OPTIONS']);
                    break;
                default:
                    if (in_array($this->enableHost)) {
                        return CorsMiddleware::addCorsHeader($handler->handle($request), $header, ['PUT', 'DELETE', 'POST', 'GET', 'PATCH', 'OPTIONS']);
                    } else {
                        $response = new ResponseSlim();
                        return $response->withStatus(403);
                    }
                    break;
            }
        } else {
            return $handler->handle($request);
        }
    }

    /**
     * @param Response $response
     * @param $allowOrigin
     * @param array $allowMethod
     * @return Response
     */
    public static function addCorsHeader(ResponseInterface $response, $allowOrigin, array $allowMethod) {
        return $response->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Origin', $allowOrigin)
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', implode( ', ', $allowMethod))
            ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->withAddedHeader('Cache-Control', 'post-check=0, pre-check=0')
            ->withHeader('Pragma', 'no-cache');
    }

    /**
     * @return boolean
     */
    public static function isXhr(Request $request) {

        $isXhr = false;
        if ($request->getHeaderLine('Origin') || $request->getHeaderLine('Referer')) {
            $isXhr = true;
        }
        return  $isXhr;
    }

    /**
     *
     */
    protected function isEnableOrigin($origin) {


    }
}