<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    $setting = [
        'settings' => [
            'determineRouteBeforeAppMiddleware' => true,
            'debug' => true,
            'displayErrorDetails' => true, // Should be set to false in production
            'logger' => [
                'name' => 'slim-app',
                'path' => __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            "mongodb" => [
                "host" => "mongo"
            ],
            'storage' => [
                'name' => 'dsign'
            ]
        ],
    ];

    $oauthSetting = include_once __DIR__ . "/../src/Module/Oauth/setting.php";
    $oauthSetting($setting);

    $userSetting = include_once __DIR__ . "/../src/Module/User/setting.php";
    $userSetting($setting);

    $monitorSetting = include_once __DIR__ . "/../src/Module/Monitor/setting.php";
    $monitorSetting($setting);


    // Global Settings Object
    $containerBuilder->addDefinitions($setting);
};
