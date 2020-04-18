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

    $userRoute = include_once __DIR__ . "/../src/Module/User/routes.php";
    $userRoute($app);

    $monitorRoute = include_once __DIR__ . "/../src/Module/Monitor/routes.php";
    $monitorRoute($app);

    $oauthRoute = include_once __DIR__ . "/../src/Module/Oauth/routes.php";
    $oauthRoute($app);

    $resourceRoute = include_once __DIR__ . "/../src/Module/Resource/routes.php";
    $resourceRoute($app);

    $timeslotRoute = include_once __DIR__ . "/../src/Module/Timeslot/routes.php";
    $timeslotRoute($app);
};
