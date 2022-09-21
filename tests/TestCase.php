<?php

namespace Tests;

use Illuminate\Support\Facades\Config;
use Tests\Fixtures\app\Models\Products\TestCustomer;
use Tests\Fixtures\app\Models\Products\TestOrder;
use Tests\Fixtures\app\Models\Products\TestOrderItem;
use Tests\Fixtures\app\Models\Products\TestProduct;

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
        //$this->loadFactoriesUsing($app, __DIR__.'/Fixtures/database/factories');

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        Config::set('laravel-cart.product_class', TestProduct::class);
        Config::set('laravel-cart.customer_class', TestCustomer::class);
        Config::set('laravel-cart.order_class', TestOrder::class);
        Config::set('laravel-cart.orderitem_class', TestOrderItem::class);
    }
}
