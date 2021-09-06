<?php
declare(strict_types=1);

use App\Controller\OptionController;
use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Middleware\Authorization\AuthorizationMiddleware;
use App\Middleware\Validation\ValidationMiddleware;
use App\Module\Restaurant\Controller\AllRpcMenuController;
use App\Module\Restaurant\Controller\MenuController;
use App\Module\Restaurant\Controller\RpcDeleteResourceMenuItem;
use App\Module\Restaurant\Controller\RpcMenuAllergensController;
use App\Module\Restaurant\Controller\RpcMenuCategoryController;
use App\Module\Restaurant\Controller\RpcUploadResourceMenuItem;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/menu', function (Group $group) {

        $group->options('', [MenuController::class, 'options']);

        $group->post('',  [MenuController::class, 'post']);

        $group->get('',  [MenuController::class, 'paginate']);

        $group->options('/{id:[0-9a-fA-F]{24}}', [MenuController::class, 'options']);

        $group->put('/{id:[0-9a-fA-F]{24}}', [MenuController::class, 'put']);

        $group->get('/{id:[0-9a-fA-F]{24}}',  [MenuController::class, 'get']);

        $group->delete('/{id:[0-9a-fA-F]{24}}',  [MenuController::class, 'delete']);

        $group->options('/upload-resource', [OptionController::class, 'options']);

        $group->post('/upload-resource',  [RpcUploadResourceMenuItem::class, 'rpc']);

        $group->options('/delete-resource', [OptionController::class, 'options']);

        $group->post('/delete-resource',  [RpcDeleteResourceMenuItem::class, 'rpc']);

        $group->get('/all',  [AllRpcMenuController::class, 'rpc']);

        $group->options('/all', [OptionController::class, 'options']);
    })
        ->add($app->getContainer()->get(ValidationMiddleware::class))
        ->add($app->getContainer()->get(AuthorizationMiddleware::class))
        ->add($app->getContainer()->get(AuthenticationMiddleware::class))
    ;

    $app->options('/menu-category', [OptionController::class, 'options']);

    $app->get('/menu-category', [RpcMenuCategoryController::class, 'rpc']);

    $app->get('/menu-allergen', [RpcMenuAllergensController::class, 'rpc']);
};
