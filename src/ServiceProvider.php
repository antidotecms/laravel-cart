<?php

namespace Antidote\LaravelCart;

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
    }

    private function migrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

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
            __DIR__.'/../config/laravel-cart.php' => config_path('laravel-cart.php'),
        ]);

        $this->mergeConfigFrom(__DIR__.'/../config/laravel-cart.php','laravel-cart-config' );
    }
}
