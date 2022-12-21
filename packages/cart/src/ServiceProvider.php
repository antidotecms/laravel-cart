<?php

namespace Antidote\LaravelCart;

use Illuminate\Support\Facades\Config;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->bindings();
    }

    public function boot()
    {
        $this->migrations();
        $this->configuration();
        $this->loadRoutesFrom(__DIR__.'../../routes/web.php');

        //create customer guard
        Config::set('auth.guards.customer', [
            'driver' => 'session',
            'provider' => 'customers'
        ]);

        Config::set('auth.providers.customers', [
            'driver' => 'eloquent',
            'model' => config('laravel-cart.classes.customer')
        ]);
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

        $this->app->bind('cart', function () {
            return new \Antidote\LaravelCart\Domain\Cart();
        });

    }

    private function configuration()
    {
        $this->publishes([
            __DIR__ . '/../../../config/laravel-cart.php' => config_path('laravel-cart.php'),
        ], 'laravel-cart-config');

        $this->mergeConfigFrom(__DIR__ . '/../../../config/laravel-cart.php','laravel-cart' );
    }
}
