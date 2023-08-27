<?php

namespace Antidote\LaravelCartFilament;

use Antidote\LaravelCart\Testing\Mixins\FilamentAssertionsMixin;
use Antidote\LaravelCart\Tests\Assertions\Livewire\LivewireAssertionsMixin;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource;
use Antidote\LaravelCartFilament\Resources\CustomerResource;
use Antidote\LaravelCartFilament\Resources\OrderResource;
use Filament\PluginServiceProvider;
use Livewire\Testing\TestableLivewire;
use Spatie\LaravelPackageTools\Package;

class FilamentServiceProvider extends PluginServiceProvider
{
//    protected array $resources = [
//        OrderResource::class,
//        CustomerResource::class,
//    ];

    public function getResources(): array
    {
        return array_merge(
            [
                'order' => OrderResource::class,
                'customer' => CustomerResource::class,
                'adjustment' => AdjustmentResource::class
            ],
            config('laravel-cart.filament')
        );
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-cart-filament')
            ->hasViews('laravel-cart-filament');
    }

    public function boot()
    {
        parent::boot();

        TestableLivewire::mixin(new FilamentAssertionsMixin());
    }
}
