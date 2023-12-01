<?php
declare(strict_types=1);

use App\Controller\OptionController;
use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Middleware\Authentication\InjectOrganizationByRoleMiddleware;
use App\Middleware\Authorization\AuthorizationMiddleware;
use App\Middleware\QueryString\QueryStringMiddleware;
use App\Middleware\Validation\ValidationMiddleware;
use App\Module\Playlist\Controller\AllRpcPlaylistController;
use App\Module\Playlist\Controller\PlaylistController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->group('/playlist', function (Group $group) {

        $group->options('', [PlaylistController::class, 'options']);

        $group->get('', [PlaylistController::class, 'paginate']);

        $group->get('/{id:[0-9a-fA-F]{24}}', [PlaylistController::class, 'get']);

        $group->post('', [PlaylistController::class, 'post']);

        $group->put('/{id:[0-9a-fA-F]{24}}', [PlaylistController::class, 'put']);

        $group->patch('/{id:[0-9a-fA-F]{24}}', [PlaylistController::class, 'patch']);

        $group->options('/{id:[0-9a-fA-F]{24}}', [PlaylistController::class, 'options']);

        $group->delete('/{id:[0-9a-fA-F]{24}}', [PlaylistController::class, 'delete']);
        
        $group->get('/all', [AllRpcPlaylistController::class, 'rpc']);

        $group->options('/all', [OptionController::class, 'options']);
    })
        ->add($app->getContainer()->get(ValidationMiddleware::class))
        ->add($app->getContainer()->get(QueryStringMiddleware::class))
        ->add($app->getContainer()->get(InjectOrganizationByRoleMiddleware::class))
        ->add($app->getContainer()->get(AuthorizationMiddleware::class))
        ->add($app->getContainer()->get(AuthenticationMiddleware::class))
    ;
};
