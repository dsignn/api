<?php
declare(strict_types=1);

namespace App\Module\Oauth\Controller;

use App\Module\Oauth\Exception\UsernameConflictException;
use App\Module\Oauth\Exception\UsernameNotFoundException;
use App\Module\Oauth\Exception\UserNotEnableException;
use App\Module\User\Entity\UserEntity;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\StreamFactory;

/**
 * Class OauthController
 * @package App\Module\Oauth\Controller
 */
class OauthController {

    /**
     * @var AuthorizationServer
     */
    protected $oauthServer;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * OauthController constructor.
     * @param AuthorizationServer $oauthServer
     */
    public function __construct(AuthorizationServer $oauthServer, ContainerInterface $container) {
        $this->oauthServer = $oauthServer;
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function accessToken(Request $request, Response $response) {

        try {
            // Try to respond to the request
            return $this->oauthServer->respondToAccessTokenRequest($request, $response);

        } catch (OAuthServerException $exception) {
            // All instances of OAuthServerException can be formatted into a HTTP response
            return $exception->generateHttpResponse($response);

        } catch (\Exception $exception) {

            $streamFactory = new StreamFactory();
            switch (true) {
                case $exception instanceof UsernameConflictException === true:
                    $response =  $response->withStatus(409)->withBody($streamFactory->createStream(
                        $exception->getMessage()
                    ));
                    break;
                case $exception instanceof UserNotEnableException === true:
                    $response =  $response->withStatus(401)->withBody($streamFactory->createStream(
                        $exception->getMessage()
                    ));
                    break;
                case $exception instanceof UsernameNotFoundException === true:
                    $response =  $response->withStatus(404)->withBody($streamFactory->createStream(
                        $exception->getMessage()
                    ));
                    break;
                default:
                    $response =  $response->withStatus(500)->withBody($streamFactory->createStream(
                        $exception->getMessage()
                    ));
            }

            return $response;
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function authorize(Request $request, Response $response) {

        try {
            // Validate the HTTP request and return an AuthorizationRequest object.
            $authRequest = $this->oauthServer->validateAuthorizationRequest($request);

            // The auth request object can be serialized and saved into a user's session.
            // You will probably want to redirect the user at this point to a login endpoint.

            // Once the user has logged in set the user on the AuthorizationRequest
            $authRequest->setUser(new UserEntity()); // an instance of UserEntityInterface

            // At this point you should redirect the user to an authorization page.
            // This form will ask the user to approve the client and the scopes requested.

            // Once the user has approved or denied the client update the status
            // (true = approved, false = denied)
            $authRequest->setAuthorizationApproved(true);

            // Return the HTTP redirect response
            return $this->oauthServer->completeAuthorizationRequest($authRequest, $response);

        } catch (OAuthServerException $exception) {

            // All instances of OAuthServerException can be formatted into a HTTP response
            return $exception->generateHttpResponse($response);

        } catch (\Exception $exception) {

            $streamFactory = new StreamFactory();
            return $response->withStatus(500)->withBody($streamFactory->createStream(
                $exception->getMessage()
            ));
        }
    }
}