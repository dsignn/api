<?php
declare(strict_types=1);

use App\Module\Oauth\Controller\OauthController;
use Slim\App;

return function (App $app) {

    $app->group('/resource', function (Group $group) {

        $group->get('', [MonitorController::class, 'paginate']);

        $group->get('/{id:[0-9a-fA-F]{24}}',  [MonitorController::class, 'get']);

        $group->post('',  [MonitorController::class, 'post']);

        $group->put('/{id:[0-9a-fA-F]{24}}',  [MonitorController::class, 'put']);

        $group->patch('/{id:[0-9a-fA-F]{24}}',  [MonitorController::class, 'patch']);

        $group->delete('/{id:[0-9a-fA-F]{24}}',  [MonitorController::class, 'delete']);
    })->add(
        new ValidationMiddleware(
            $app->getContainer()->get('settings')['validation'],
            $app->getContainer()
        ))->add(new OAuthMiddleware(
        $app->getContainer()->get(ResourceServer::class),
        $app->getContainer()->get(UserStorageInterface::class),
        $app->getContainer()->get('AccessTokenStorage')
    ));
};
