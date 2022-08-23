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
    }

    private function migrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations/my-package'),
        ], 'laravel-cart');
    }

    private function bindings()
    {

        $this->app->bind('cart', function () {
            return new \Antidote\LaravelCart\Domain\Cart();
        });

    }
}
