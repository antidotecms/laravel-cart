<?php

namespace Antidote\LaravelCartStripe;


use Antidote\LaravelCart\Commands\CreateMigrationCommand;
use Antidote\LaravelCartStripe\Components\StripeCheckoutClientScriptComponent;
use Illuminate\Support\Facades\Blade;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if($this->app->runningInConsole()) {
            $this->commands([
                CreateMigrationCommand::class
            ]);
        }

        $this->loadRoutesFrom(__DIR__.'../../routes/web.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-cart-stripe');

        Blade::component('stripe-checkout-client-script', StripeCheckoutClientScriptComponent::class);

    }
}
