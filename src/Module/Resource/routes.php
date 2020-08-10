<?php
declare(strict_types=1);

use App\Middleware\Validation\ValidationMiddleware;
use App\Module\Resource\Controller\AllRpcResourceController;
use App\Module\Resource\Controller\ResourceController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/resource', function (Group $group) {

        $group->options('', [ResourceController::class, 'options']);

        $group->get('', [ResourceController::class, 'paginate']);

        $group->get('/{id:[0-9a-fA-F]{24}}',  [ResourceController::class, 'get']);

        $group->post('',  [ResourceController::class, 'post']);

        $group->patch('/{id:[0-9a-fA-F]{24}}',  [ResourceController::class, 'patch']);

        $group->options('/{id:[0-9a-fA-F]{24}}',  [ResourceController::class, 'options']);

        $group->delete('/{id:[0-9a-fA-F]{24}}',  [ResourceController::class, 'delete']);

        $group->get('/all',  [AllRpcResourceController::class, 'rpc']);

    })
        ->add($app->getContainer()->get(ValidationMiddleware::class))
        //->add($app->getContainer()->get(AuthenticationMiddleware::class))
    ;
};
