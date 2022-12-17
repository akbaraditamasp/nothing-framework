<?php

use Bramus\Router\Router;
use Nothing\App;

require __DIR__ . "/../vendor/autoload.php";

$router = new Router();

$router->setNamespace("\App\Controllers");

require __DIR__ . "/../routes/routes.php";

$router->run(function () {
    App::handle();
});
