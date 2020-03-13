<?php
declare(strict_types=1);

namespace App\Middleware\Validation;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class ValidationMiddleware
 * @package App\Middleware\Validation
 */
class ValidationMiddleware implements Middleware {

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $path = $request->getAttribute('__route__')->getPattern();
        $method = $request->getMethod();
    //    var_dump($request->getAttribute('oauth_client_obj'));
    //    die();

        return $handler->handle($request);
    }
}