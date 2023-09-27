<?php
declare(strict_types=1);

use App\Middleware\Validation\ValidationMiddleware;
use App\Module\Device\Controller\DeviceController;
use App\Module\Device\Controller\DeviceUpsertRpcRestController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/device', function (Group $group) {

        $group->options('', [DeviceController::class, 'options']);

        $group->post('',  [DeviceUpsertRpcRestController::class, 'rpc']);

        $group->get('', [DeviceController::class, 'paginate']);

    })->add($app->getContainer()->get(ValidationMiddleware::class))
       // ->add($app->getContainer()->get(AuthorizationMiddleware::class))
      //  ->add($app->getContainer()->get(AuthenticationMiddleware::class))
    ;
};
