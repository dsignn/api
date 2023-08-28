<?php
declare(strict_types=1);

use App\Middleware\Validation\ValidationMiddleware;
use App\Module\Machine\Controller\MachineController;
use App\Module\Machine\Controller\MachineUpsertRpcRestController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/machine', function (Group $group) {

        $group->options('', [MachineController::class, 'options']);

        $group->post('',  [MachineUpsertRpcRestController::class, 'rpc']);

    })->add($app->getContainer()->get(ValidationMiddleware::class))
       // ->add($app->getContainer()->get(AuthorizationMiddleware::class))
      //  ->add($app->getContainer()->get(AuthenticationMiddleware::class))
    ;
};
