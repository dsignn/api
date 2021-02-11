<?php
declare(strict_types=1);

use App\Controller\OptionController;
use App\Middleware\Validation\ValidationMiddleware;
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

        $group->patch('/{id:[0-9a-fA-F]{24}}',  [UserController::class, 'patch']);

        $group->options('/{id:[0-9a-fA-F]{24}}', [UserController::class, 'options']);

        $group->delete('/{id:[0-9a-fA-F]{24}}',  [UserController::class, 'delete']);
    })
        ->add($app->getContainer()->get(ValidationMiddleware::class))
     //   ->add($app->getContainer()->get(AuthorizationMiddleware::class))
       // ->add($app->getContainer()->get(AuthenticationMiddleware::class))
    ;

    $app->post('/recover-password', [PasswordToken::class, 'rpc']);

    $app->options('/recover-password', [OptionController::class, 'options']);

    $app->post('/reset-password', [ResetPassword::class, 'rpc']);

    $app->options('/reset-password', [OptionController::class, 'options']);

    $app->get('/activation-code', [ActivationToken::class, 'rpc']);

    $app->options('/activation-code', [OptionController::class, 'options']);
};
