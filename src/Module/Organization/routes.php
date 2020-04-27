<?php
declare(strict_types=1);

use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Middleware\Validation\ValidationMiddleware;
use App\Module\Organization\Controller\OrganizationController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/organization', function (Group $group) {

        $group->get('', [OrganizationController::class, 'paginate']);

        $group->get('/{id:[0-9a-fA-F]{24}}',  [OrganizationController::class, 'get']);

        $group->post('',  [OrganizationController::class, 'post']);

        $group->put('/{id:[0-9a-fA-F]{24}}',  [OrganizationController::class, 'put']);

        $group->delete('/{id:[0-9a-fA-F]{24}}',  [OrganizationController::class, 'delete']);
    })->add($app->getContainer()->get(ValidationMiddleware::class))
        ->add($app->getContainer()->get(AuthenticationMiddleware::class));
};
