<?php

namespace Spatie\Translatable\Test;

use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Translatable\TranslatableServiceProvider;

abstract class MySQLTestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase($this->app);
    }

    /**
     * Get Package Service Provider.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [TranslatableServiceProvider::class];
    }

    /**
     * Setup MySQL Environment. Need to setup environment variable in phpunit.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver'      => 'mysql',
            'host'        => env('DB_HOST', '127.0.0.1'),
            'port'        => env('DB_PORT', '3306'),
            'database'    => env('DB_DATABASE'),
            'username'    => env('DB_USERNAME'),
            'password'    => env('DB_PASSWORD'),
            'unix_socket' => env('DB_SOCKET'),
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ]);
    }

    /**
     * Create test models table if not exits.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
        $schemaBuilder = $app['db']->connection()->getSchemaBuilder();

        if (!$schemaBuilder->hasTable('test_models')) {
            $schemaBuilder->create('test_models', function (Blueprint $table) {
                $table->increments('id');
                $table->json('name')->nullable();
                $table->json('other_field')->nullable();
            });
        }
    }
}
