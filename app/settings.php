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

    $monitorSetting = include_once __DIR__ . "/../src/Module/Monitor/setting.php";
    $monitorSetting($setting);

    $resourceSetting = include_once __DIR__ . "/../src/Module/Resource/setting.php";
    $resourceSetting($setting);

    $machineSetting = include_once __DIR__ . "/../src/Module/Machine/setting.php";
    $machineSetting($setting);

    $localSetting = include_once __DIR__ . "/local-setting.php";
    $localSetting($setting);

    // Global Settings Object
    $containerBuilder->addDefinitions($setting);
};
