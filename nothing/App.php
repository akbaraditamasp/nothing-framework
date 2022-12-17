<?php

namespace Nothing;

use Exception;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;

class App
{
    /**
     * Request
     * @var Laminas\Diactoros\ServerRequest
     */
    public $request;

    /**
     * Response
     * @var Laminas\Diactoros\Response
     */
    public $response;

    /**
     * Uploaded
     * @var Laminas\Diactoros\UploadedFileFactory
     */
    public $uploaded;

    /**
     * Stream
     * @var Laminas\Diactoros\StreamFactory
     */
    public $stream;

    /**
     * Callback
     * @var mixed
     */
    private static $cb;

    public function __construct()
    {
        $this->request = ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
        $this->response = new Response();
        $this->uploaded = new UploadedFileFactory();
        $this->stream = new StreamFactory;

        BodyParser::boot($this);
        Cors::boot($this);
    }

    public function finish()
    {
        (new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($this->response);
        exit();
    }

    public static function setController(callable $cb)
    {
        static::$cb = $cb;
    }

    public static function handle()
    {
        $app = new App();
        try {
            $app->response = $app->response->withHeader("Content-Type", "application/json");
            $payload = json_encode((App::$cb)($app));
            $app->response->getBody()->write($payload);
            $app->finish();
        } catch (Exception $e) {
            $app->response->getBody()->write(json_encode([
                "error" => "Internal error"
            ]));
            $app->response = $app->response->withStatus(404)->withHeader("Content-Type", "application/json");
            $app->finish();
        }
    }
}
