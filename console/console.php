<?php
// application.php

require __DIR__ . '/../vendor/autoload.php';

use DI\ContainerBuilder;
use Symfony\Component\Console\Application;

$containerBuilder = new ContainerBuilder();

// Set up settings
$settings = require __DIR__ . '/../app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__ . '/../app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

$container = $containerBuilder->build();

$application = $container->get(Application::class);

$commands = require __DIR__ . "/../app/commands.php";
$commands($application, $container);

$application->run();