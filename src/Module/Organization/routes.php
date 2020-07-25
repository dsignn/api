<?php
declare(strict_types=1);

use App\Controller\OptionController;
use App\Middleware\Validation\ValidationMiddleware;
use App\Module\Organization\Controller\AllRpcOrganizationController;
use App\Module\Organization\Controller\GenerateQrCodeRpc;
use App\Module\Organization\Controller\OrganizationController;
use App\Module\Resource\Controller\AllRpcResourceController;
use App\Module\User\Controller\PasswordToken;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/organization', function (Group $group) {

        $group->get('', [OrganizationController::class, 'paginate']);

        $group->options('', [OrganizationController::class, 'options']);

        $group->get('/{id:[0-9a-fA-F]{24}}',  [OrganizationController::class, 'get']);

        $group->post('',  [OrganizationController::class, 'post']);

        $group->put('/{id:[0-9a-fA-F]{24}}',  [OrganizationController::class, 'put']);

        $group->delete('/{id:[0-9a-fA-F]{24}}',  [OrganizationController::class, 'delete']);

        $group->get('/all',  [AllRpcOrganizationController::class, 'rpc']);

        $group->options('/all',  [OptionController::class, 'options']);
    })
        ->add($app->getContainer()->get(ValidationMiddleware::class))
        //->add($app->getContainer()->get(AuthenticationMiddleware::class))
    ;

    $app->get('/generate-qrcode/{id:[0-9a-fA-F]{24}}', [GenerateQrCodeRpc::class, 'rpc']);
};
