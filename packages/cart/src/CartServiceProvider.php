<?php

namespace Antidote\LaravelCart;

use Antidote\LaravelCart\Domain\Cart;
use Antidote\LaravelCart\Http\Controllers\OrderCompleteController;
use Antidote\LaravelCart\Http\Controllers\OrderController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class CartServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        //$this->mergeConfigFrom(__DIR__ . '/../../../config/laravel-cart.php','laravel-cart' );
        $this->bindings();

        Model::shouldBeStrict();
    }

    public function boot()
    {
        $this->routes();
        $this->migrations();
        $this->configuration();
        //$this->loadRoutesFrom(__DIR__.'../../routes/web.php');

        //create customer guard
        Config::set('auth.guards.customer', [
            'driver' => 'session',
            'provider' => 'customers'
        ]);

        $this->app->booted(function() {
            Config::set('auth.providers.customers', [
                'driver' => 'eloquent',
                'model' => app()->get('filament')->getPlugin('laravel-cart')->getModel('customer')
            ]);
        });

    }

    private function migrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-cart');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'laravel-cart-migrations');
    }

    private function bindings()
    {
        $this->app->bind(Cart::class, Cart::class);
    }

    private function configuration()
    {
        $this->publishes([
            __DIR__ . '/../../../config/laravel-cart.php' => config_path('laravel-cart.php'),
        ], 'laravel-cart-config');
    }

    private function routes()
    {
        $this->app->booted(function() {
            $this->app['router']->get(app('filament')->getPlugin('laravel-cart')->getOrderCompleteUrl(), OrderCompleteController::class)
                    ->middleware(['web', 'auth:customer'])->name('laravel-cart.order_complete');

            $this->app['router']->get('/checkout/replace_cart/{order_id}', [OrderController::class, 'setOrderItemsAsCart'])
                ->middleware(['web', 'auth:customer'])->name('laravel-cart.replace_cart');;

            $this->app['router']->get('/checkout/add_to_cart/{order_id}', [OrderController::class, 'addOrderItemsToCart'])
                ->middleware(['web', 'auth:customer'])->name('laravel-cart.add_to_cart');
        });

    }
}
