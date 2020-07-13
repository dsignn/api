<?php
declare(strict_types=1);

use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Middleware\Authorization\AuthorizationMiddleware;
use App\Module\User\Controller\ActivationToken;
use App\Module\User\Controller\PasswordToken;
use App\Module\User\Controller\ResetPassword;
use App\Module\User\Controller\UserController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/user', function (Group $group) {

        $group->options('', [UserController::class, 'options']);

        $group->get('', [UserController::class, 'paginate']);

        $group->get('/{id:[0-9a-fA-F]{24}}',  [UserController::class, 'get']);

        $group->post('',  [UserController::class, 'post']);

        $group->put('/{id:[0-9a-fA-F]{24}}',  [UserController::class, 'put']);

        $group->options('/{id:[0-9a-fA-F]{24}}', [UserController::class, 'options']);

        $group->delete('/{id:[0-9a-fA-F]{24}}',  [UserController::class, 'delete']);
    })
        //->add($app->getContainer()->get(ValidationMiddleware::class))
     //   ->add($app->getContainer()->get(AuthorizationMiddleware::class))
       // ->add($app->getContainer()->get(AuthenticationMiddleware::class))
    ;

    $app->post('/recover-password', [PasswordToken::class, 'rpc']);

    $app->post('/reset-password', [ResetPassword::class, 'rpc']);

    $app->get('/activation', [ActivationToken::class, 'rpc']);
};
