<?php
declare(strict_types=1);

use App\Controller\OptionController;
use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Middleware\Validation\ValidationMiddleware;
use App\Module\Oauth\Controller\ClientController;
use App\Module\Oauth\Controller\MeController;
use App\Module\Oauth\Controller\MyOrgController;
use App\Module\Oauth\Controller\OauthController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->post('/access-token', [OauthController::class, 'accessToken']);

    $app->options('/access-token', [OptionController::class, 'options']);

    $app->get('/authorize', [OauthController::class, 'authorize']);

    $app->options('/me', [MeController::class, 'options']);

    $app->get('/me', [MeController::class, 'rpc'])
        ->add($app->getContainer()->get(AuthenticationMiddleware::class));
    
    $app->get('/my-org', [MyOrgController::class, 'rpc'])
        ->add($app->getContainer()->get(AuthenticationMiddleware::class));

    /**
     * Client oauth
     */
    $app->group('/oauth', function (Group $group) {
        
        $group->options('/client', [ClientController::class, 'options']);

        $group->get('/client', [ClientController::class, 'paginate']);

        $group->post('/client',  [ClientController::class, 'post']);
    })
        ->add($app->getContainer()->get(ValidationMiddleware::class))
    ;
};
