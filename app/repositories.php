<?php
declare(strict_types=1);

use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {

    $userRepositories = include_once __DIR__ . "/../src/Module/User/repositories.php";
    $userRepositories($containerBuilder);

    $monitorRepositories = include_once __DIR__ . "/../src/Module/Monitor/repositories.php";
    $monitorRepositories($containerBuilder);

    $oauthRepositories = include_once __DIR__ . "/../src/Module/Oauth/repositories.php";
    $oauthRepositories($containerBuilder);
};
