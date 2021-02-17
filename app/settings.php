<?php
declare(strict_types=1);

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {

    $setting = [];

    $oauthSetting = include_once __DIR__ . "/../src/Module/Oauth/setting.php";
    $oauthSetting($setting);

    $userSetting = include_once __DIR__ . "/../src/Module/User/setting.php";
    $userSetting($setting);

    $organizationSetting = include_once __DIR__ . "/../src/Module/Organization/setting.php";
    $organizationSetting($setting);

    $localSetting = include_once __DIR__ . "/local-setting.php";
    $localSetting($setting);

    $monitorSetting = include_once __DIR__ . "/../src/Module/Monitor/setting.php";
    $monitorSetting($setting);

    $resourceSetting = include_once __DIR__ . "/../src/Module/Resource/setting.php";
    $resourceSetting($setting);

    $timeslotSetting = include_once __DIR__ . "/../src/Module/Timeslot/setting.php";
    $timeslotSetting($setting);

    $restaurantSetting = include_once __DIR__ . "/../src/Module/Restaurant/setting.php";
    $restaurantSetting($setting);

    var_dump($setting['settings']['s3Resource']);
    die();

    // Global Settings Object
    $containerBuilder->addDefinitions($setting);
};
