<?php
declare(strict_types=1);

use App\Middleware\AuthMiddleware;
use App\Middleware\CorsMiddleware;
use Slim\App;

return function (App $app) {

    $app->add(new CorsMiddleware());
};