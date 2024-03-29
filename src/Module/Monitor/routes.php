<?php
declare(strict_types=1);

use App\Controller\OptionController;
use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Middleware\Authentication\InjectOrganizationByRoleMiddleware;
use App\Middleware\Authorization\AuthorizationMiddleware;
use App\Middleware\QueryString\QueryStringMiddleware;
use App\Middleware\Validation\ValidationMiddleware;
use App\Module\Monitor\Controller\AllRpcMonitorController;
use App\Module\Monitor\Controller\MonitorController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/monitor', function (Group $group) {

        $group->options('', [MonitorController::class, 'options']);

        $group->get('', [MonitorController::class, 'paginate']);

        $group->post('',  [MonitorController::class, 'post']);

        $group->options('/{id:[0-9a-fA-F]{24}}',  [MonitorController::class, 'options']);

        $group->get('/{id:[0-9a-fA-F]{24}}',  [MonitorController::class, 'get']);

        $group->put('/{id:[0-9a-fA-F]{24}}',  [MonitorController::class, 'put']);

        $group->patch('/{id:[0-9a-fA-F]{24}}',  [MonitorController::class, 'patch']);

        $group->delete('/{id:[0-9a-fA-F]{24}}',  [MonitorController::class, 'delete']);

        $group->get('/all',  [AllRpcMonitorController::class, 'rpc']);

        $group->options('/all', [OptionController::class, 'options']);

    })  
        ->add($app->getContainer()->get(ValidationMiddleware::class))
        ->add($app->getContainer()->get(QueryStringMiddleware::class))
        ->add($app->getContainer()->get(InjectOrganizationByRoleMiddleware::class))
        ->add($app->getContainer()->get(AuthorizationMiddleware::class))
        ->add($app->getContainer()->get(AuthenticationMiddleware::class))
        
    ;
};
