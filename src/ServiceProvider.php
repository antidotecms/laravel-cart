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
    }

    private function bindings()
    {
        $this->app->bind(Cart::class, function($app) {
                return auth()->check() ? auth()->user()->cart : null;
            });

        $this->app->bind('cart', function ($app) {
            return new \Antidote\LaravelCart\Domain\Cart();
        });

    }
}
