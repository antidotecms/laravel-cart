<?php

namespace Antidote\LaravelCart\Tests;

use Antidote\LaravelCart\Tests\Fixtures\cart\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\TestOrder;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\TestOrderAdjustment;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\TestOrderItem;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\TestPaymentMethod;
use Illuminate\Support\Facades\Config;

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

        Config::set('laravel-cart.classes.product', TestProduct::class);
        Config::set('laravel-cart.classes.customer', TestCustomer::class);
        Config::set('laravel-cart.classes.order', TestOrder::class);
        Config::set('laravel-cart.classes.order_item', TestOrderItem::class);
        Config::set('laravel-cart.classes.order_adjustment', TestOrderAdjustment::class);
        Config::set('laravel-cart.classes.payment_method', TestPaymentMethod::class);
    }
}
