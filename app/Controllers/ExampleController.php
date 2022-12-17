<?php

namespace App\Controllers;

use Nothing\App;

class ExampleController
{
    public function hello()
    {
        App::setController(function () {
            return ["message" => "Hello world!"];
        });
    }
}
