<?php
declare(strict_types=1);

namespace App\Middleware\Authentication;

use App\Module\Oauth\Entity\AccessTokenEntity;
use App\Module\Oauth\Entity\ClientEntity;
use App\Module\Organization\Entity\OrganizationEntity;
use App\Module\User\Entity\UserEntity;
use App\Storage\StorageInterface;
use Exception;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

/**
 * Class AuthenticationMiddleware
 * @package App\Middleware
 */
class AuthenticationMiddleware implements Middleware {

    /**
     * @var ResourceServer
     */
    protected $server;

    /**
     * @var StorageInterface
     */
    protected $userStorage;

    /**
     * @var StorageInterface
     */
    protected $organizationStorage;

    /**
     * @var StorageInterface
     */
    protected $tokenStorage;

    /**
     * @var StorageInterface
     */
    protected $clientStorage;

    /**
     * @var array
     */
    protected $settings;

    /**
     * AuthenticationMiddleware constructor.
     * @param ResourceServer $server
     * @param StorageInterface $userStorage
     * @param StorageInterface $organizationStorage
     * @param StorageInterface $tokenStorage
     * @param StorageInterface $clientStorage
     * @param array $settings
     */
    public function __construct(ResourceServer $server, 
        StorageInterface $userStorage, 
        StorageInterface $organizationStorage, 
        StorageInterface $tokenStorage, 
        StorageInterface $clientStorage, 
        array $settings = []) {

        $this->server = $server;
        $this->userStorage = $userStorage;
        $this->organizationStorage = $organizationStorage;
        $this->tokenStorage = $tokenStorage;
        $this->clientStorage = $clientStorage;
        $this->settings = $settings;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        // TODO refactor add in other middleware
        if (isset($request->getQueryParams()['auth'])) {
            $request = $request->withAddedHeader('authorization', 'Bearer ' . $request->getQueryParams()['auth']);        
        }

        if (($this->isPublic($request) && !$request->getHeaderLine('authorization')) || $request->getMethod() === 'OPTIONS' ) {
            return $handler->handle($request);
        }

        try {
            $request = $this->server->validateAuthenticatedRequest($request);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse(new Response());
        } catch (Exception $exception) {
            return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error', 500))
                ->generateHttpResponse(new Response());
        }

        return $handler->handle(
            $request->withAttribute('app-user', $this->getUser($request->getAttribute('oauth_user_id')))
                ->withAttribute('app-organization', $this->getOrganization($request->getAttribute('oauth_user_id')))
                ->withAttribute('app-client', $this->getClient($request->getAttribute('oauth_access_token_id')))
        );
    }

    /**
     * @param $identifier
     * @return UserEntity|null
     */
    protected function getUser($identifier) {

        // TODO get aggregate query
        $user = null;
        if ($identifier) {
            $resultSet = $this->userStorage->getAll(
                ['email' => $identifier]
            );

            $user = $resultSet->current();

            if ($user) {
                $organizations = $user->getOrganizations(); 
                $orgs = [];
                foreach ($organizations as &$value) {
                   array_push($orgs, $this->organizationStorage->get($value->getId())); 
                }

                $user->setOrganizations($orgs); 
            }
        }
       
        return $user;
    }

    /**
     * @param $identifier
     * @return OrganizationEntity|null
     */
    protected function getOrganization($identifier) {
        $org = null;
        if (str_contains($identifier, 'organization_')) {

            $stringId = str_replace("organization_", "", "$identifier");
    
            try { 
                $org = $this->organizationStorage->get($stringId);
            } catch (Exception $e) {
                // TODO Log error
            }
        }

        return $org;
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
                $resultSetClient = $this->clientStorage->getAll(['name' => $token->getClient()->getName()]);
                if ($resultSetClient->count() > 0) {
                    $client = $resultSetClient->current();
                }
            }
        }

        return $client;
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     */
    protected function isPublic(ServerRequestInterface $request) {

        $isPublic = false;
        $path = $request->getAttribute('__route__')->getPattern();
        if (isset($this->settings[$path]) && 
            is_array($this->settings[$path]) && 
            isset($this->settings[$path][$request->getMethod()]) &&
            isset($this->settings[$path][$request->getMethod()]['public'])) {

            $isPublic = $this->settings[$path][$request->getMethod()]['public'];
        }

        return $isPublic;
    }
} 