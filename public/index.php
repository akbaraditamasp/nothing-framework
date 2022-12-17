<?php

use Bramus\Router\Router;
use Nothing\App;
use Nothing\Eloquent;

require __DIR__ . "/../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

Eloquent::boot();
$router = new Router();

$router->setNamespace("\App\Controllers");

require __DIR__ . "/../routes/routes.php";

$router->run(function () {
    App::handle();
});
