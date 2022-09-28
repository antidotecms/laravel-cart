<?php

namespace Antidote\LaravelCartStripe;

use Antidote\LaravelCartStripe\Commands\MakeStripePaymentTableCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if($this->app->runningInConsole()) {
            $this->commands([
                MakeStripePaymentTableCommand::class
            ]);
        }
    }
}
