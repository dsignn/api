<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('pong');
        return $response;
    });

    $oauthRoute = include_once __DIR__ . "/../src/Module/Oauth/routes.php";
    $oauthRoute($app);

    $userRoute = include_once __DIR__ . "/../src/Module/User/routes.php";
    $userRoute($app);

    $organizationRoute = include_once __DIR__ . "/../src/Module/Organization/routes.php";
    $organizationRoute($app);

    $monitorRoute = include_once __DIR__ . "/../src/Module/Monitor/routes.php";
    $monitorRoute($app);

    $resourceRoute = include_once __DIR__ . "/../src/Module/Resource/routes.php";
    $resourceRoute($app);

    $orderRoute = include_once __DIR__ . "/../src/Module/Order/routes.php";
    $orderRoute($app);
};
