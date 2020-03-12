<?php
declare(strict_types=1);

use App\Module\Oauth\Console\CreateClientCommand;
use App\Module\Oauth\Console\GeneratePasswordCommand;
use App\Module\Oauth\Console\GeneratePrivateKeyCommand;
use App\Module\Oauth\Console\GeneratePublicKeyCommand;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

return function (Application $app, ContainerInterface $container) {

    $app->add(new GeneratePasswordCommand());
    $app->add(new GeneratePrivateKeyCommand());
    $app->add(new GeneratePublicKeyCommand());
    $app->add(new CreateClientCommand($container->get('ClientStorage'), $container->get('OAuthCrypto')));
};