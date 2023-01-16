<?php

namespace Antidote\LaravelCartFilament;

use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class ServiceProvider extends PluginServiceProvider
{
//    protected array $resources = [
//        OrderResource::class,
//        CustomerResource::class,
//    ];

    public function getResources(): array
    {
        //dd(config('laravel-cart'));
//        return [
//            config('laravel-cart.filament.order'),
//            config('laravel-cart.filament.customer')
//        ];
        return config('laravel-cart.filament');
//        return [
//            'order' => \App\Filament\Cart\Resources\OrderResource::class,
//            'customer' => \Antidote\LaravelCartFilament\Resources\CustomerResource::class
//        ];
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-cart-filament')
            ->hasViews('laravel-cart-filament');
    }
}
