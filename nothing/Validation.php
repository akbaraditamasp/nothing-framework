<?php

namespace Nothing;

use EndyJasmi\Cuid;
use Laminas\Diactoros\UploadedFile;
use Nothing\App;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;

class Validation
{
    public static function validate(array $rules, App $app)
    {
        $body = $app->request->getParsedBody();
        $query = $app->request->getQueryParams();
        $file = $app->request->getUploadedFiles();

        $data = [];
        try {
            if (is_array($rules["body"] ?? [])) {
                foreach ($rules["body"] ?? [] as $field => $rule) {
                    $data[$field] = $body[$field] ?? null;
                    $rule->assert($body[$field] ?? null);
                }
            } else {
                $revalidate = $body;
                array_walk($revalidate, function (&$key) use ($rules) {
                    $key = $rules["body"];
                });
                $data = static::validate([
                    "body" => $revalidate
                ], $app) + $data;
            }

            if (is_array($rules["query"] ?? [])) {
                foreach ($rules["query"] ?? [] as $field => $rule) {
                    $data[$field] = $query[$field] ?? null;
                    $rule->assert($query[$field] ?? null);
                }
            } else {
                $revalidate = $query;
                array_walk($revalidate, function (&$key) use ($rules) {
                    $key = $rules["query"];
                });
                $data = static::validate([
                    "query" => $revalidate
                ], $app) + $data;
            }

            if (is_array($rules["file"] ?? [])) {
                foreach ($rules["file"] ?? [] as $field => $rule) {
                    /**
                     * @var ?UploadedFile $uploaded
                     */
                    $uploaded = isset($file[$field]) ? $file[$field] : null;

                    if (Validator::objectType()->attribute("file", Validator::file())->validate($uploaded)) {
                        Validator::objectType()->attribute("file", $rule)->assert($uploaded);
                        $data[$field] = $uploaded;
                    } else if (Validator::objectType()->attribute("stream")->validate($uploaded)) {
                        $cache = sys_get_temp_dir() . "/" . Cuid::cuid();
                        stream_copy_to_stream($uploaded->getStream()->detach(), fopen($cache, "w"));

                        $uploadedTmp = new UploadedFile($cache, $uploaded->getSize(), $uploaded->getError(), $uploaded->getClientFilename());

                        $rule->assert($cache);

                        $data[$field] = $uploadedTmp;
                    } else {
                        $rule->assert($uploaded);

                        $data[$field] = $uploaded;
                    }
                }
            } else {
                $revalidate = $file;
                array_walk($revalidate, function (&$key) use ($rules) {
                    $key = $rules["file"];
                });
                $data = static::validate([
                    "file" => $revalidate
                ], $app) + $data;
            }
        } catch (NestedValidationException $e) {
            $app->response->getBody()->write(json_encode($e->getMessages()));
            $app->response = $app->response->withStatus(400)->withHeader("Content-Type", "application/json");
            $app->finish();
        }

        return $data;
    }
}