<?php

namespace Antidote\LaravelCartFilament;

use Antidote\LaravelCartFilament\Resources\CustomerResource;
use Antidote\LaravelCartFilament\Resources\OrderResource;
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
        return config('laravel-cart.filament') ?? [
            'order' => OrderResource::class,
            'customer' => CustomerResource::class
            ];
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
