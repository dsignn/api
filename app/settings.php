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
            ],
            'mail' => [
                "url" => "http://127.0.0.150/reset-password",
                "port" => 587,
                "host" => 'smtp.gmail.com',
                "username" => 'antonino.visalli@gmail.com',
                "password" => 'xhveatyfvxscmrco'
            ]
        ],
    ];

    $oauthSetting = include_once __DIR__ . "/../src/Module/Oauth/setting.php";
    $oauthSetting($setting);

    $userSetting = include_once __DIR__ . "/../src/Module/User/setting.php";
    $userSetting($setting);

    $monitorSetting = include_once __DIR__ . "/../src/Module/Monitor/setting.php";
    $monitorSetting($setting);

    $resourceSetting = include_once __DIR__ . "/../src/Module/Resource/setting.php";
    $resourceSetting($setting);

    $timeslotSetting = include_once __DIR__ . "/../src/Module/Timeslot/setting.php";
    $timeslotSetting($setting);


    // Global Settings Object
    $containerBuilder->addDefinitions($setting);
};
