<?php
declare(strict_types=1);

use App\Middleware\AuthenticationMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\Validation\ValidationMiddleware;
use App\Module\Timeslot\Controller\TimeslotController;
use App\Module\User\Storage\UserStorageInterface;
use League\OAuth2\Server\ResourceServer;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/timeslot', function (Group $group) {

        $group->get('', [TimeslotController::class, 'paginate']);

        $group->get('/{id:[0-9a-fA-F]{24}}',  [TimeslotController::class, 'get']);

        $group->post('',  [TimeslotController::class, 'post']);

        $group->put('/{id:[0-9a-fA-F]{24}}',  [TimeslotController::class, 'put']);

        $group->delete('/{id:[0-9a-fA-F]{24}}',  [TimeslotController::class, 'delete']);
    })->add(
        new ValidationMiddleware(
            $app->getContainer()->get('settings')['validation'],
            $app->getContainer()
    ))->add(new AuthenticationMiddleware(
        $app->getContainer()->get(ResourceServer::class),
        $app->getContainer()->get(UserStorageInterface::class),
        $app->getContainer()->get('AccessTokenStorage')
    ));
};
