<?php
declare(strict_types=1);

use Slim\App;

return function (App $app) {


    $restaurantRoute = include_once __DIR__ . "/../src/Module/Restaurant/menu-routes.php";
    $restaurantRoute($app);
};
