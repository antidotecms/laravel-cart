<?php

namespace Antidote\LaravelCart\Tests;

use Antidote\LaravelCart\Http\Controllers\OrderCompleteController;
use Antidote\LaravelCart\ServiceProvider;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrder;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderAdjustment;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderItem;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderLogItem;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestPayment;
use Illuminate\Support\Facades\Config;

class StripeTestCase extends \Orchestra\Testbench\TestCase
{
//    protected function migrateUsing()
//    {
//        return [
//            '--path' => [
//                './database/migrations/stripe'
//            ]
//        ];
//    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/Fixtures/Cart/migrations');
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        Config::set('laravel-cart.classes.product', TestProduct::class);
        Config::set('laravel-cart.classes.customer', Customer::class);
        Config::set('laravel-cart.classes.order', TestOrder::class);
        Config::set('laravel-cart.classes.order_item', TestOrderItem::class);
        Config::set('laravel-cart.classes.order_adjustment', TestOrderAdjustment::class);
        Config::set('laravel-cart.classes.payment', TestPayment::class);
        Config::set('laravel-cart.classes.order_log_item', TestOrderLogItem::class);
        Config::set('laravel-cart.tax_rate', 0.2);

        //parent::getEnvironmentSetUp($app); // TODO: Change the autogenerated stub

        //Config::set('laravel-cart.classes.order', TestStripeOrder::class);
        //Config::set('laravel-cart.classes.order_log_item', TestStripeOrderLogItem::class);
    }

    protected function defineRoutes($router)
    {
        $router->get('/login')->name('login');
        $router->get('/order-complete', OrderCompleteController::class)->middleware('auth:customer');
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
            \Antidote\LaravelCartStripe\ServiceProvider::class
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'cart' => \Antidote\LaravelCart\Domain\Cart::class
        ];
    }
}
