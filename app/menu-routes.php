<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {


    $restaurantRoute = include_once __DIR__ . "/../src/Module/Restaurant/menu-routes.php";
    $restaurantRoute($app);
};
