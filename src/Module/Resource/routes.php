<?php
declare(strict_types=1);

use App\Middleware\AuthenticationMiddleware;
use App\Middleware\Validation\ValidationMiddleware;
use App\Module\Resource\Controller\ResourceController;
use App\Module\User\Storage\UserStorageInterface;
use League\OAuth2\Server\ResourceServer;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/resource', function (Group $group) {

        $group->get('', [ResourceController::class, 'paginate']);

        $group->get('/{id:[0-9a-fA-F]{24}}',  [ResourceController::class, 'get']);

        $group->post('',  [ResourceController::class, 'post']);

        $group->put('/{id:[0-9a-fA-F]{24}}',  [ResourceController::class, 'put']);

       // $group->patch('/{id:[0-9a-fA-F]{24}}',  [MonitorController::class, 'patch']);

        $group->delete('/{id:[0-9a-fA-F]{24}}',  [ResourceController::class, 'delete']);
    })->add(
        new ValidationMiddleware(
            $app->getContainer()->get('settings')['validation'],
            $app->getContainer()
        ))
    ->add(new AuthenticationMiddleware(
        $app->getContainer()->get(ResourceServer::class),
        $app->getContainer()->get(UserStorageInterface::class),
        $app->getContainer()->get('AccessTokenStorage')
    ));
};
