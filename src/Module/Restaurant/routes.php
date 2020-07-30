<?php
declare(strict_types=1);

use App\Module\Restaurant\Controller\MenuController;
use App\Module\Restaurant\Controller\RpcMenuController;
use App\Module\Timeslot\Controller\TimeslotController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/restaurant', function (Group $group) {

        $group->options('/{idOrganization:[0-9a-fA-F]{24}}/menu', [MenuController::class, 'options']);

        $group->post('/{idOrganization:[0-9a-fA-F]{24}}/menu',  [MenuController::class, 'post']);

        $group->get('/{idOrganization:[0-9a-fA-F]{24}}/menu',  [MenuController::class, 'paginate']);

        $group->get('/{idOrganization:[0-9a-fA-F]{24}}/menu/{id:[0-9a-fA-F]{24}}',  [MenuController::class, 'get']);

        $group->delete('/{idOrganization:[0-9a-fA-F]{24}}/menu/{id:[0-9a-fA-F]{24}}',  [MenuController::class, 'delete']);

    })
        //->add($app->getContainer()->get(ValidationMiddleware::class))
        //->add($app->getContainer()->get(AuthenticationMiddleware::class))
    ;

    $app->get('/restaurant-menu/{slug}',  [RpcMenuController::class, 'rpc']);
};
