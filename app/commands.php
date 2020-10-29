<?php

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

return function (Application $app, ContainerInterface $container) {

    $oauthCommands = include_once __DIR__ . "/../src/Module/Oauth/commands.php";
    $oauthCommands($app, $container);

    $userCommands = include_once __DIR__ . "/../src/Module/User/commands.php";
    $userCommands($app, $container);

    $restaurantCommands = include_once __DIR__ . "/../src/Module/Restaurant/commands.php";
    $restaurantCommands($app, $container);

};