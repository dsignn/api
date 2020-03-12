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
        if ($this->isXhr($request)) {
            switch (true) {
                case $this->isWildCard === true:
                    $response =  $handler->handle($request);
                    return $this->addCorsHeader($response,'*', ['PUT', 'DELETE', 'POST', 'GET']);
                    break;
                default:

                    if (in_array($this.$this->enableHost)) {
                        $response =  $handler->handle($request);
                        return $this->addCorsHeader($response, $header, ['PUT', 'DELETE', 'POST', 'GET']);
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
    protected function addCorsHeader(ResponseInterface $response, $allowOrigin, array $allowMethod) {

        return $response->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Origin', $allowOrigin)
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', implode( ',', $allowMethod))
            ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->withAddedHeader('Cache-Control', 'post-check=0, pre-check=0')
            ->withHeader('Pragma', 'no-cache');
    }

    /**
     * @return boolean
     */
    protected function isXhr(Request $request) {

        $isXhr = false;
        if ($request->getHeaderLine('X_REQUESTED_WITH') === 'XMLHttpRequest') {
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