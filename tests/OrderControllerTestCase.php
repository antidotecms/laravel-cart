<?php

namespace Antidote\LaravelCart\Tests;

use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrder;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderControllerTestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected function defineDatabaseMigrations()
    {
        //$this->loadMigrationsFrom(__DIR__.'../../packages/cart/database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/Fixtures/Cart/migrations');
    }

    protected function defineEnvironment($app)
    {
        $cart_plugin = (new CartPanelPlugin())
            ->models(['order' => TestOrder::class])
            ->urls(['orderComplete' => 'order-complete']);

        setUpCartPlugin($cart_plugin);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->config->set('laravel-cart.views.order_complete', 'laravel-cart::order-complete');
    }

    protected function defineRoutes($router)
    {
        $router->get('login')->name('login');
    }
}
