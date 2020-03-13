<?php
declare(strict_types=1);

use App\Middleware\AuthMiddleware;
use App\Middleware\OAuthMiddleware;
use App\Middleware\Validation\ValidationMiddleware;
use App\Module\User\Controller\UserController;
use App\Module\User\Storage\UserStorageInterface;
use League\OAuth2\Server\ResourceServer;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/user', function (Group $group) {

        $group->get('', [UserController::class, 'paginate']);

        $group->get('/{id:[0-9a-fA-F]{24}}',  [UserController::class, 'get']);

        $group->post('',  [UserController::class, 'post']);

        $group->put('/{id:[0-9a-fA-F]{24}}',  [UserController::class, 'put']);

        $group->delete('/{id:[0-9a-fA-F]{24}}',  [UserController::class, 'delete']);
    })->add(
        new ValidationMiddleware(
            $app->getContainer()->get('settings')['validation'],
            $app->getContainer()
    ))->add(new OAuthMiddleware(
        $app->getContainer()->get(ResourceServer::class),
        $app->getContainer()->get(UserStorageInterface::class),
        $app->getContainer()->get('AccessTokenStorage')
    ));

};
