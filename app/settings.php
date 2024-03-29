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

    $playlistSetting = include_once __DIR__ . "/../src/Module/Playlist/setting.php";
    $playlistSetting($setting);

    $deviceSetting = include_once __DIR__ . "/../src/Module/Device/setting.php";
    $deviceSetting($setting);

    $localSetting = include_once __DIR__ . "/local-setting.php";
    $localSetting($setting);

    // Global Settings Object
    $containerBuilder->addDefinitions($setting);
};
