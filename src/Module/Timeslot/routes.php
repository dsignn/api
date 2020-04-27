<?php
declare(strict_types=1);

use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\Validation\ValidationMiddleware;
use App\Module\Timeslot\Controller\TimeslotController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/timeslot', function (Group $group) {

        $group->get('', [TimeslotController::class, 'paginate']);

        $group->get('/{id:[0-9a-fA-F]{24}}',  [TimeslotController::class, 'get']);

        $group->post('',  [TimeslotController::class, 'post']);

        $group->put('/{id:[0-9a-fA-F]{24}}',  [TimeslotController::class, 'put']);

        $group->delete('/{id:[0-9a-fA-F]{24}}',  [TimeslotController::class, 'delete']);
    })->add($app->getContainer()->get(ValidationMiddleware::class))
        ->add($app->getContainer()->get(AuthenticationMiddleware::class));
};
