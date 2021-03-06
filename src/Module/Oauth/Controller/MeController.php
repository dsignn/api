<?php

namespace App\Module\Oauth\Controller;

use App\Controller\RpcControllerInterface;
use App\Middleware\ContentNegotiation\AcceptServiceAwareTrait;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class MeController
 * @package App\Module\Oauth\Controller
 */
class MeController implements RpcControllerInterface {

    use AcceptServiceAwareTrait;

    /**
     * @var string
     */
    protected $hydratorService = 'RestUserEntityHydrator';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * MeController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function rpc(Request $request, Response $response) {
        $user = $request->getAttribute('app-user');

        if (!$user) {
            return $response->withStatus(404);
        }

        $acceptService = $this->getAcceptService($request);
        return $acceptService->transformAccept($response, $user);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function options(Request $request, Response $response) {
        return $response;
    }
}