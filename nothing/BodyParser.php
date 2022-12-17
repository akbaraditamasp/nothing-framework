<?php

namespace Nothing;

use Exception;
use Kekos\MultipartFormDataParser\Parser;
use Respect\Validation\Validator;

class BodyParser
{
    public static function boot(App $app)
    {
        if (
            $app->request->getHeaderLine("content-type") === "application/json"
        ) {
            if (Validator::json()->validate($app->request->getBody()->getContents())) {
                $input = json_decode($app->request->getBody()->getContents(), TRUE);
                $app->request = $app->request->withParsedBody($input);
            }
        } else if ($app->request->getMethod() !== "POST") {
            try {
                $parser = Parser::createFromRequest($app->request, $app->uploaded, $app->stream);
                $app->request = $parser->decorateRequest($app->request);
            } catch (Exception $e) {
            }
        }
    }
}
