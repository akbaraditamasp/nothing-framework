<?php

namespace Nothing;

use Medz\Cors\Cors as CorsLib;

class Cors
{
    public static function boot(App $app, $config = [
        'allow-credentials' => false, // set "Access-Control-Allow-Credentials" 👉 string "false" or "true".
        'allow-headers'      => ['*'], // ex: Content-Type, Accept, X-Requested-With
        'expose-headers'     => [],
        'origins'            => ['*'], // ex: http://localhost
        'methods'            => ['*'], // ex: GET, POST, PUT, PATCH, DELETE
        'max-age'            => 0,
    ])
    {
        $cors = new CorsLib($config);
        $cors->setRequest('psr-7', $app->request);
        $cors->setResponse('psr-7', $app->response);
        $cors->handle();

        $app->response = $cors->getResponse();

        if ($app->request->getMethod() === "OPTIONS") {
            $app->response = $cors->getResponse();
            $app->finish();
        }
    }
}
