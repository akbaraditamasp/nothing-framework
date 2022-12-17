<?php
namespace Nothing;

use Illuminate\Database\Capsule\Manager;

class Eloquent {
    /**
     * Manager
     * @var Illuminate\Database\Capsule\Manager
     */
    private static Manager $manager;

    public static function boot() {
        static::$manager = new Manager;

static::$manager->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'database',
    'username' => 'root',
    'password' => 'password',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
static::$manager->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
static::$manager->bootEloquent();
    }

    public static function getManager() {
        return static::$manager
    }
}