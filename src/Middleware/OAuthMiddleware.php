<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Module\Oauth\Entity\AccessTokenEntity;
use App\Module\Oauth\Entity\ClientEntity;
use App\Module\User\Entity\UserEntity;
use App\Storage\StorageHydrateInterface;
use Exception;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

/**
 * Class OAuthMiddleware
 * @package App\Middleware
 */
class OAuthMiddleware implements Middleware {

    /**
     * @var ResourceServer
     */
    protected $server;

    /**
     * @var StorageHydrateInterface
     */
    protected $userStorage;

    /**
     * @var StorageHydrateInterface
     */
    protected $tokenStorage;

    /**
     * AuthMiddleware constructor.
     * @param ResourceServer $server
     * @param StorageHydrateInterface $userStorage
     * @param StorageHydrateInterface $tokenStorage
     */
    public function __construct(ResourceServer $server, StorageHydrateInterface $userStorage, StorageHydrateInterface $tokenStorage) {
        $this->server = $server;
        $this->userStorage = $userStorage;
        $this->tokenStorage = $tokenStorage;
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

        return $handler->handle(
            $request->withAttribute('oauth_user_obj', $this->getUser($request->getAttribute('oauth_user_id')))
                ->withAttribute('oauth_client_obj', $this->getClient($request->getAttribute('oauth_access_token_id')))
        );
    }

    /**
     * @param $identifier
     * @return UserEntity|null
     */
    protected function getUser($identifier) {

        $user = null;
        if ($identifier) {
            $resultSet = $this->userStorage->getAll(
                ['identifier' => $identifier]
            );

            $user = $resultSet->current();
        }

        return $user;
    }

    /**
     * @param $accessTokenIdentifier
     * @return ClientEntity|null
     */
    protected function getClient($accessTokenIdentifier) {
        $client = null;
        if ($accessTokenIdentifier) {

            $resultSet = $this->tokenStorage->getAll(
                ['identifier' => $accessTokenIdentifier]
            );

            /** @var AccessTokenEntity $token */
            $token = $resultSet->current();
            if ($token) {
                $client = $token->getClient();
            }
        }

        return $client;
    }
}