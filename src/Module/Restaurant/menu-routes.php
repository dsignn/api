<?php
declare(strict_types=1);

use App\Middleware\ContentNegotiation\Accept\AcceptContainer;
use App\Middleware\ContentNegotiation\Accept\JsonAccept;
use App\Middleware\ContentNegotiation\ContentNegotiationMiddleware;
use App\Middleware\ContentNegotiation\ContentType\ContentTypeContainer;
use App\Middleware\ContentNegotiation\ContentType\JsonContentType;
use App\Module\Restaurant\Controller\RpcFrontendRedirectController;
use App\Module\Restaurant\Controller\RpcMenuController;
use App\Module\Restaurant\Controller\RpcPrintQrcodeController;
use App\Module\Restaurant\Middleware\Accept\MenuAccept;
use Slim\App;
use Slim\Views\Twig;

return function (App $app) {

    attachMenuAcceptService($app);

    $app->get('/', [RpcFrontendRedirectController::class, 'rpc']);

    $app->get('/{slug}',  [RpcMenuController::class, 'rpc'])->add(
        getContentNegotiationMiddleware($app)
    );

    $app->get('/print-qrcode/{id:[0-9a-fA-F]{24}}',  [RpcPrintQrcodeController::class, 'rpc']);
};

/**
 * @param App $app
 * @throws Exception
 */
function attachMenuAcceptService(App $app) {

    $menuAccept = new MenuAccept(
        $app->getContainer()->get(Twig::class),
        $app->getContainer()
    );

    $app->getContainer()->get(AcceptContainer::class)->set(
        MenuAccept::class,
        $menuAccept
    );
}

/**
 * @param App $app
 */
function getContentNegotiationMiddleware(App $app) {

    $contentNegotiationMiddleware = new ContentNegotiationMiddleware($app->getContainer()->get('settings')['contentNegotiation']);
    $contentNegotiationMiddleware->setAcceptContainer($app->getContainer()->get(AcceptContainer::class))
        ->setContentTypeContainer($app->getContainer()->get(ContentTypeContainer::class));
    $contentNegotiationMiddleware->setDefaultAcceptServices([
        'application/json' => JsonAccept::class
    ])->setDefaultContentTypeServices([
        'application/json' => JsonContentType::class
    ]);

    return $contentNegotiationMiddleware;
}
