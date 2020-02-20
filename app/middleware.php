<?php
declare(strict_types=1);

use App\Middleware\CorsMiddleware;
use App\Middleware\RestContentMiddleware;
use App\Middleware\SessionMiddleware;
use App\Middleware\ContentNegotiationMiddleware;
use Slim\App;

return function (App $app) {

    createContentNegotiation();
    $app->add(new SessionMiddleware());
    $app->add(new ContentNegotiationMiddleware());
    $app->add(new CorsMiddleware());
};


function createContentNegotiation() {

    var_dump('test');
    die();
}