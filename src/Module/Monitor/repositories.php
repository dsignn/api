<?php
declare(strict_types=1);

use App\Module\Monitor\Storage\MonitorStorage;
use App\Module\Monitor\Storage\MonitorStorageInterface;
use DI\ContainerBuilder;
use MongoDB\Client;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {

    $containerBuilder->addDefinitions([
        MonitorStorageInterface::class => function(ContainerInterface $c) {
            $settings = $c->get('settings');
            $storageSetting = $settings['storage'];
            return new MonitorStorage($c->get(Client::class), $storageSetting['name'], $storageSetting['monitor']['collection']);
        }
    ]);
};
