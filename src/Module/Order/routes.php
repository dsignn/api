<?php
declare(strict_types=1);

use App\Controller\OptionController;
use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Middleware\Authorization\AuthorizationMiddleware;
use App\Middleware\Validation\ValidationMiddleware;
use App\Module\Order\Controller\AllRpcOrderController;
use App\Module\Order\Controller\OrderController;
use App\Module\Order\Middleware\CorsOrderAuthentication;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/order', function (Group $group) {

        $group->get('', [OrderController::class, 'paginate']);

        $group->options('', [OptionController::class, 'options']);

        $group->get('/{id:[0-9a-fA-F]{24}}',  [OrderController::class, 'get']);

        $group->post('',  [OrderController::class, 'post']);

        $group->put('/{id:[0-9a-fA-F]{24}}',  [OrderController::class, 'put']);

        $group->options('/{id:[0-9a-fA-F]{24}}', [OrderController::class, 'options']);

        $group->delete('/{id:[0-9a-fA-F]{24}}',  [OrderController::class, 'delete']);

        $group->get('/all',  [AllRpcOrderController::class, 'rpc']);

        $group->options('/all',  [OrderController::class, 'options']);
    })
        ->add($app->getContainer()->get(ValidationMiddleware::class))
        ->add($app->getContainer()->get(AuthorizationMiddleware::class))  
        ->add($app->getContainer()->get(CorsOrderAuthentication::class))
        
    ;
};
