<?php

namespace Antidote\LaravelCart;

use Antidote\LaravelCart\Http\Controllers\OrderCompleteController;
use Antidote\LaravelCart\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../../config/laravel-cart.php','laravel-cart' );
        $this->bindings();

        Model::shouldBeStrict();
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

        $this->app->when(OrderCompleteController::class)
            ->needs(Order::class)
            ->give(function($app) {
                if($order_id = request()->get('order_id')) {
                    $order = getClassNameFor('order')::where('id', $order_id)->first();
                    if (auth('customer')->check() && $order && $order->customer->id == auth()->guard('customer')->user()->id) {
                        $order->load('items.product.productType');
                        return $order;
                    }
                }

                return null;
            });
    }

    private function configuration()
    {
        $this->publishes([
            __DIR__ . '/../../../config/laravel-cart.php' => config_path('laravel-cart.php'),
        ], 'laravel-cart-config');
    }
}
