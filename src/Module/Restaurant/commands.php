<?php
declare(strict_types=1);

use App\Module\Restaurant\Console\CreateMenuCategoryCommand;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

return function (Application $app, ContainerInterface $container) {

    $app->add(new CreateMenuCategoryCommand($container->get(MenuCategoryStorageInterface::class)));
};