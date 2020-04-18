<?php
declare(strict_types=1);

use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {

    $oauthRepositories = include_once __DIR__ . "/../src/Module/Oauth/repositories.php";
    $oauthRepositories($containerBuilder);

    $userRepositories = include_once __DIR__ . "/../src/Module/User/repositories.php";
    $userRepositories($containerBuilder);

    $monitorRepositories = include_once __DIR__ . "/../src/Module/Monitor/repositories.php";
    $monitorRepositories($containerBuilder);

    $resourceRepositories = include_once __DIR__ . "/../src/Module/Resource/repositories.php";
    $resourceRepositories($containerBuilder);

    $timeslotRepositories = include_once __DIR__ . "/../src/Module/Timeslot/repositories.php";
    $timeslotRepositories($containerBuilder);
};
