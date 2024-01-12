<?php

namespace Antidote\LaravelCart;

use Antidote\LaravelCart\Domain\Cart;
use Antidote\LaravelCart\Http\Controllers\CheckoutConfirmController;
use Antidote\LaravelCart\Http\Controllers\EmailVerificationController;
use Antidote\LaravelCart\Http\Controllers\OrderController;
use Antidote\LaravelCart\Http\Controllers\PostCheckoutController;
use Antidote\LaravelCart\Http\Middleware\EnsureOrderBelongsToCustomer;
use Antidote\LaravelCart\Livewire\Cart\CartItem;
use Antidote\LaravelCart\Livewire\Cart\Checkout;
use Antidote\LaravelCart\Livewire\Cart\CheckoutOptions;
use Antidote\LaravelCart\Livewire\Cart\Icon;
use Antidote\LaravelCart\Livewire\Customer\Address;
use Antidote\LaravelCart\Livewire\Customer\Dashboard;
use Antidote\LaravelCart\Livewire\Customer\Details;
use Antidote\LaravelCart\Livewire\Customer\Login;
use Antidote\LaravelCart\Livewire\Customer\Menu;
use Antidote\LaravelCart\Livewire\Customer\Registration;
use Antidote\LaravelCart\Livewire\Product;
use Antidote\LaravelCart\Livewire\TestComponent;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
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

        Blade::componentNamespace('Antidote\\LaravelCart\\Components', 'laravel-cart');

        $this->callAfterResolving(BladeCompiler::class, function () {
            Livewire::component('laravel-cart::product', Product::class);
            Livewire::component('laravel-cart::cart', \Antidote\LaravelCart\Livewire\Cart\Cart::class);
            Livewire::component('laravel-cart::cart.item', CartItem::class);
            Livewire::component('laravel-cart::customer.login', Login::class);
            Livewire::component('laravel-cart::customer.dashboard', Dashboard::class);
            Livewire::component('laravel-cart::customer.details', Details::class);
            Livewire::component('laravel-cart::customer.address', Address::class);
            Livewire::component('laravel-cart::cart.icon', Icon::class);
            Livewire::component('laravel-cart::cart.checkout-options', CheckoutOptions::class);
            Livewire::component('laravel-cart::cart.checkout', Checkout::class);
            Livewire::component('laravel-cart::customer.menu', Menu::class);
            Livewire::component('laravel-cart::customer.registration', Registration::class);
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

            $this->app['router']->get(CartPanelPlugin::get('urls.registration'), function() {
                return view(CartPanelPlugin::get('views.registration'));
            })->middleware(['web']);

            $this->app['router']->get(CartPanelPlugin::get('urls.orderComplete'), function() {
                return view(CartPanelPlugin::get('views.orderComplete'));
            })->middleware(['web', 'auth:customer', EnsureOrderBelongsToCustomer::class]);

            $this->app['router']->prefix(CartPanelPlugin::get('urls.cart'))->group(function() {

                $this->app['router']->get('/', function() {
                    return view(CartPanelPlugin::get('views.cart'));
                })->middleware(['web']);

                $this->app['router']->get('replace_cart/{order_id}', [OrderController::class, 'setOrderItemsAsCart'])
                    ->middleware(['web', 'auth:customer'])->name('laravel-cart.replace_cart');

                $this->app['router']->get('add_to_cart/{order_id}', [OrderController::class, 'addOrderItemsToCart'])
                    ->middleware(['web', 'auth:customer'])->name('laravel-cart.add_to_cart');
            });

            $this->app['router']->prefix(CartPanelPlugin::get('urls.customer'))->group(function() {

                $this->app['router']->get('login', function() {
                    //@todo would be nice to leverage the apps RouteServiceProvider use use the HOME constant but difficult to test
                    if (Auth::guard('customer')->check()) {
                        return redirect(CartPanelPlugin::get('urls.customer').'/dashboard');
                    }
                    return view('login');
                })->middleware(['web'])->name('login');

                $this->app['router']->get('dashboard', function() {
                    return view('customer.dashboard');
                })->middleware(['web', 'auth:customer', 'verified']);

            });

            $this->app['router']->prefix(CartPanelPlugin::get('urls.shop'))->group(function() {

                $this->app['router']->get('checkout', function () {
                    return view('checkout');
                })->middleware(['web', 'auth:customer']);

            });

//            $this->app['router']->get('/checkout/replace_cart/{order_id}', [OrderController::class, 'setOrderItemsAsCart'])
//                ->middleware(['web', 'auth:customer'])->name('laravel-cart.replace_cart');

//            $this->app['router']->get('/checkout/add_to_cart/{order_id}', [OrderController::class, 'addOrderItemsToCart'])
//                ->middleware(['web', 'auth:customer'])->name('laravel-cart.add_to_cart');

            $this->app['router']->get(\Antidote\LaravelCartFilament\CartPanelPlugin::get('urls.checkoutConfirm'), CheckoutConfirmController::class)
                ->middleware(['web']);

            $this->app['router']->get(\Antidote\LaravelCartFilament\CartPanelPlugin::get('urls.postCheckout'), PostCheckoutController::class)
                ->middleware(['web']);

            $this->app['router']->middleware(['web', 'auth:customer'])->prefix('auth')->group(function() {

                $this->app['router']->get('email-verification/{id}/{hash}', [EmailVerificationController::class, 'verify'])
                    ->middleware(['signed'])
                    ->name('verification.verify');

                $this->app['router']->get('verify', [EmailVerificationController::class, 'notice'])
                    ->name('verification.notice');

                $this->app['router']->get('verification-notification', [EmailVerificationController::class, 'send'])
                    ->middleware(['throttle:6,1'])
                    ->name('verification.send');
            });

        });

    }
}
