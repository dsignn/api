<?php

use App\Module\User\Console\CreateUserCommand;
use App\Module\User\Storage\UserStorageInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

/**
 * @param Application $app
 * @param ContainerInterface $container
 */
return function (Application $app, ContainerInterface $container) {

    $app->add(new CreateUserCommand($container->get(UserStorageInterface::class), $container->get('OAuthCrypto')));
};
