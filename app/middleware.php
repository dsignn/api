<?php
declare(strict_types=1);

use App\Middleware\CorsMiddleware;
use App\Middleware\SessionMiddleware;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

return function (App $app) {

    $app->add(new SessionMiddleware());

    $app->add($app->getContainer()->get('ContentNegotiationMiddleware'));

    $app->add(TwigMiddleware::create($app, $app->getContainer()->get(Twig::class)));

    $app->add(new CorsMiddleware());
};