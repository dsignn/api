<?php
declare(strict_types=1);

use App\Controller\OptionController;
use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Middleware\Authorization\AuthorizationMiddleware;
use App\Middleware\Validation\ValidationMiddleware;
use App\Module\Organization\Controller\AccessTokenOrganizationRcp;
use App\Module\Organization\Controller\AllRpcOrganizationController;
use App\Module\Organization\Controller\OrganizationController;
use App\Module\Organization\Controller\RpcUploadResourceOrganization;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/organization', function (Group $group) {

        $group->get('', [OrganizationController::class, 'paginate']);

        $group->options('', [OrganizationController::class, 'options']);

        $group->get('/{id:[0-9a-fA-F]{24}}',  [OrganizationController::class, 'get']);

        $group->post('',  [OrganizationController::class, 'post']);

        $group->put('/{id:[0-9a-fA-F]{24}}',  [OrganizationController::class, 'put']);

        $group->options('/{id:[0-9a-fA-F]{24}}', [OrganizationController::class, 'options']);

        $group->delete('/{id:[0-9a-fA-F]{24}}',  [OrganizationController::class, 'delete']);

        $group->get('/all',  [AllRpcOrganizationController::class, 'rpc']);

        $group->options('/all',  [OptionController::class, 'options']);

        $group->options('/upload-resource', [OptionController::class, 'options']);

        $group->post('/upload-resource',  [RpcUploadResourceOrganization::class, 'rpc']);

    })->add($app->getContainer()->get(ValidationMiddleware::class))
      ->add($app->getContainer()->get(AuthorizationMiddleware::class))
      ->add($app->getContainer()->get(AuthenticationMiddleware::class));
};
