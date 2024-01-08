<?php
declare(strict_types=1);

namespace App\Middleware\Language;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;

class LanguageMiddleware implements Middleware {

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        var_dump($request->getHeaderLine('Accept-Language'));
        die();
        // TODO: Implement process() method.
    }
}