<?php

namespace Antidote\LaravelCartStripe;


use Antidote\LaravelCart\Commands\CreateMigrationCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if($this->app->runningInConsole()) {
            $this->commands([
                CreateMigrationCommand::class
            ]);
        }
    }
}
