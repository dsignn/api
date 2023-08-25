<?php
declare(strict_types=1);


use App\Module\Machine\Controller\MachineController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/machine', function (Group $group) {

        $group->options('', [MachineController::class, 'options']);

        $group->get('', [MachineController::class, 'paginate']);

        $group->post('',  [MachineController::class, 'post']);

        $group->options('/{id:[0-9a-fA-F]{24}}',  [MachineController::class, 'options']);

        $group->get('/{id:[0-9a-fA-F]{24}}',  [MachineController::class, 'get']);

        $group->put('/{id:[0-9a-fA-F]{24}}',  [MachineController::class, 'put']);

        $group->patch('/{id:[0-9a-fA-F]{24}}',  [MachineController::class, 'patch']);

        $group->delete('/{id:[0-9a-fA-F]{24}}',  [MachineController::class, 'delete']);
    })//->add($app->getContainer()->get(ValidationMiddleware::class))
       // ->add($app->getContainer()->get(AuthorizationMiddleware::class))
      //  ->add($app->getContainer()->get(AuthenticationMiddleware::class))
    ;
};
