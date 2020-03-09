<?php
declare(strict_types=1);

use App\Middleware\ContentNegotiation\Accept\AcceptContainer;
use App\Middleware\ContentNegotiation\Accept\JsonAccept;
use App\Middleware\ContentNegotiation\ContentNegotiationMiddleware;
use App\Middleware\ContentNegotiation\ContentType\ContentTypeContainer;
use App\Middleware\ContentNegotiation\ContentType\JsonContentType;
use App\Middleware\CorsMiddleware;
use App\Middleware\SessionMiddleware;
use Slim\App;

return function (App $app) {

    $app->add(new SessionMiddleware());

    $contentNegotiationMiddleware = new ContentNegotiationMiddleware($app->getContainer()->get('settings')['contentNegotiation']);
    $contentNegotiationMiddleware->setAcceptContainer($app->getContainer()->get(AcceptContainer::class))
        ->setContentTypeContainer($app->getContainer()->get(ContentTypeContainer::class));
    $contentNegotiationMiddleware->setDefaultAcceptServices([
        'application/json' => JsonAccept::class
    ])->setDefaultContentTypeServices([
        'application/json' => JsonContentType::class
    ]);

    $app->add($contentNegotiationMiddleware);

    $app->add(new CorsMiddleware());
};