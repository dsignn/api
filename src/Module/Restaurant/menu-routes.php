<?php
declare(strict_types=1);

use App\Controller\OptionController;
use App\Middleware\Authentication\AuthenticationMiddleware;
use App\Middleware\Authorization\AuthorizationMiddleware;
use App\Middleware\Validation\ValidationMiddleware;
use App\Module\Restaurant\Controller\MenuController;
use App\Module\Restaurant\Controller\RpcDeleteResourceMenuItem;
use App\Module\Restaurant\Controller\RpcMenuCategoryController;
use App\Module\Restaurant\Controller\RpcMenuController;
use App\Module\Restaurant\Controller\RpcPrintQrcodeController;
use App\Module\Restaurant\Controller\RpcUploadResourceMenuItem;
use App\Module\Timeslot\Controller\TimeslotController;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    $app->get('/{slug}',  [RpcMenuController::class, 'rpc']);

    $app->get('/print-qrcode/{id:[0-9a-fA-F]{24}}',  [RpcPrintQrcodeController::class, 'rpc']);
};
