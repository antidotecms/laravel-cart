<?php

namespace Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/Fixtures/database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            'Antidote\LaravelCart\ServiceProvider'
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
