<?php
declare(strict_types=1);

use App\Module\Restaurant\Console\CreateMenuAllergensCommand;
use App\Module\Restaurant\Console\CreateMenuCategoryCommand;
use App\Module\Restaurant\Storage\MenuAllergensStorageInterface;
use App\Module\Restaurant\Storage\MenuCategoryStorageInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

return function (Application $app, ContainerInterface $container) {

    $app->add(new CreateMenuCategoryCommand($container->get(MenuCategoryStorageInterface::class)));
    $app->add(new CreateMenuAllergensCommand($container->get(MenuAllergensStorageInterface::class)));
};