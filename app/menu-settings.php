<?php
declare(strict_types=1);

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {

    $setting = [];

    $oauthSetting = include_once __DIR__ . "/../src/Module/Oauth/setting.php";
    $oauthSetting($setting);

    $userSetting = include_once __DIR__ . "/../src/Module/User/setting.php";
    $userSetting($setting);

    $resourceSetting = include_once __DIR__ . "/../src/Module/Resource/menu-setting.php";
    $resourceSetting($setting);

    $organizationSetting = include_once __DIR__ . "/../src/Module/Organization/menu-setting.php";
    $organizationSetting($setting);

    $restaurantSetting = include_once __DIR__ . "/../src/Module/Restaurant/menu-setting.php";
    $restaurantSetting($setting);

    $localSetting = include_once __DIR__ . "/menu-local-setting.php";
    $localSetting($setting);

    // Global Settings Object
    $containerBuilder->addDefinitions($setting);
};
