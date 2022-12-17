<?php

use Bramus\Router\Router;

require __DIR__ . "/../vendor/autoload.php";

$router = new Router();

require __DIR__ . "/../routes/routes.php";

$router->run();
