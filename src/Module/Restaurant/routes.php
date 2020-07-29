<?php
declare(strict_types=1);

use App\Module\Restaurant\Controller\RpcMenuController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/restaurant', function (Group $group) {

        $group->get('/{slug}/menu',  [RpcMenuController::class, 'rpc']);
    })
        //->add($app->getContainer()->get(ValidationMiddleware::class))
        //->add($app->getContainer()->get(AuthenticationMiddleware::class))
    ;
};
