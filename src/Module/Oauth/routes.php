<?php
declare(strict_types=1);

use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Module\Oauth\Controller\MeController;
use App\Module\Oauth\Controller\OauthController;
use Slim\App;

return function (App $app) {

    $app->post('/access-token', [OauthController::class, 'accessToken']);

    $app->get('/authorize', [OauthController::class, 'authorize']);

    $app->options('/me', [MeController::class, 'options']);

    $app->get('/me', [MeController::class, 'rpc'])
        ->add($app->getContainer()->get(AuthenticationMiddleware::class));
    ;
};
