<?php
declare(strict_types=1);

use App\Module\Oauth\Controller\OauthController;
use Slim\App;

return function (App $app) {

    $app->post('/access-token', [OauthController::class, 'accessToken']);

    $app->get('/authorize', [OauthController::class, 'authorize']);
};
