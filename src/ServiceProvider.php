<?php

namespace Antidote\LaravelCart;

use Antidote\LaravelCart\Models\Cart;

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

        $this->app->bind('cart', function ($app) {
            return new \Antidote\LaravelCart\Domain\Cart();
        });

    }
}
