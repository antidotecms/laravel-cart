<?php

namespace Antidote\LaravelCart;

use Antidote\LaravelCart\Domain\Cart;
use Antidote\LaravelCart\Http\Controllers\OrderCompleteController;
use Antidote\LaravelCart\Http\Controllers\OrderController;
use Antidote\LaravelCart\Livewire\Cart\CartItem;
use Antidote\LaravelCart\Livewire\Cart\Icon;
use Antidote\LaravelCart\Livewire\Customer\Address;
use Antidote\LaravelCart\Livewire\Customer\Dashboard;
use Antidote\LaravelCart\Livewire\Customer\Details;
use Antidote\LaravelCart\Livewire\Customer\Login;
use Antidote\LaravelCart\Livewire\Product;
use Antidote\LaravelCart\Livewire\TestComponent;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Illuminate\Support\Facades\Config;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;

class CartServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->bindings();

        app()->singleton(CartPanelPlugin::class, fn() => new CartPanelPlugin());
    }

    public function boot()
    {
        $this->routes();
        $this->migrations();
        $this->configuration();

        //create customer guard
        Config::set('auth.guards.customer', [
            'driver' => 'session',
            'provider' => 'customers'
        ]);

        $this->app->booted(function() {
            Config::set('auth.providers.customers', [
                'driver' => 'eloquent',
                'model' => CartPanelPlugin::get('models.customer')
            ]);
        });

        $this->callAfterResolving(BladeCompiler::class, function () {
            Livewire::component('laravel-cart::product', Product::class);
            Livewire::component('laravel-cart::cart', \Antidote\LaravelCart\Livewire\Cart\Cart::class);
            Livewire::component('laravel-cart::cart.item', CartItem::class);
            Livewire::component('laravel-cart::customer.login', Login::class);
            Livewire::component('laravel-cart::customer.dashboard', Dashboard::class);
            Livewire::component('laravel-cart::customer.details', Details::class);
            Livewire::component('laravel-cart::customer.address', Address::class);
            Livewire::component('laravel-cart::cart.icon', Icon::class);
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

    /** @todo remove as config now handled my CartPanelPlugin */
    private function configuration()
    {
        $this->publishes([
            __DIR__ . '/../../../config/laravel-cart.php' => config_path('laravel-cart.php'),
        ], 'laravel-cart-config');
    }

    private function routes()
    {
        $this->app->booted(function() {
            $this->app['router']
                ->get(CartPanelPlugin::get('urls.orderComplete'), OrderCompleteController::class)
                ->middleware(['web', 'auth:customer'])
                ->name('laravel-cart.order_complete');

            $this->app['router']->get('/checkout/replace_cart/{order_id}', [OrderController::class, 'setOrderItemsAsCart'])
                ->middleware(['web', 'auth:customer'])->name('laravel-cart.replace_cart');;

            $this->app['router']->get('/checkout/add_to_cart/{order_id}', [OrderController::class, 'addOrderItemsToCart'])
                ->middleware(['web', 'auth:customer'])->name('laravel-cart.add_to_cart');
        });

    }
}
