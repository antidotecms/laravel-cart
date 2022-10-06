<?php

namespace Antidote\LaravelCartFilament;

use Antidote\LaravelCartFilament\Resources\CustomerResource;
use Antidote\LaravelCartFilament\Resources\OrderResource;
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class ServiceProvider extends PluginServiceProvider
{
    protected array $resources = [
        OrderResource::class,
        CustomerResource::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-cart-filament')
            ->hasViews('laravel-cart-filament');
    }
}
