<?php
declare(strict_types=1);

use App\Module\User\Controller\UserController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

return function (App $app) {

    $app->group('/user', function (Group $group) {

        $group->get('', [UserController::class, 'paginate']);

        $group->get('/{id:[0-9a-fA-F]{24}}',  [UserController::class, 'get']);

        $group->post('',  [UserController::class, 'post']);
    });

};
