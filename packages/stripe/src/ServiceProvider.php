<?php

namespace Antidote\LaravelCartStripe;


use Antidote\LaravelCart\Commands\CreateMigrationCommand;
use Antidote\LaravelCart\Providers\EventServiceProvider;
use Antidote\LaravelCartStripe\Components\StripeCheckoutClientScriptComponent;
use Antidote\LaravelCartStripe\Http\Middleware\AllowStripeWebhooksDuringMaintenance;
use Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot(Kernel $kernel)
    {
        if($this->app->runningInConsole()) {
            $this->commands([
                CreateMigrationCommand::class
            ]);
        }

        $this->loadRoutesFrom(__DIR__.'../../routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-cart-stripe');

        $this->app->register(EventServiceProvider::class);

        Blade::component('stripe-checkout-client-script', StripeCheckoutClientScriptComponent::class);

        $router = $this->app->make(Router::class);
        $router->pushMiddlewareToGroup('stripe_webhook', WhitelistStripeIPAddresses::class);
        $router->pushMiddlewareToGroup('stripe_webhook', AllowStripeWebhooksDuringMaintenance::class);

        //$kernel->pushMiddleware(AllowStripeWebhooksDuringMaintenance::class);
    }
}
