<?php
declare(strict_types=1);

namespace App\Middleware;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Exception;

class AuthMiddleware implements Middleware {

    /**
     * @var ResourceServer
     */
    protected $server;

    /**
     * @param ResourceServer $server
     */
    public function __construct(ResourceServer $server)
    {
        $this->server = $server;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        try {
            $request = $this->server->validateAuthenticatedRequest($request);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse(new Response());
        } catch (Exception $exception) {
            return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error', 500))
                ->generateHttpResponse(new Response());
        }

        var_dump($request->getAttribute('oauth_access_token_id'));
        var_dump($request->getAttribute('oauth_client_id'));
        var_dump($request->getAttribute('oauth_user_id'));
        var_dump($request->getAttribute('oauth_scopes'));
        die();
        // Pass the request and response on to the next responder in the chain
        return $handler->handle($request);
    }
}