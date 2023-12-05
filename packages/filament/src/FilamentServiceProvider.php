<?php

namespace Antidote\LaravelCartFilament;

use Antidote\LaravelCart\Testing\Mixins\FilamentAssertionsMixin;
use Antidote\LaravelCart\Tests\Assertions\Livewire\LivewireAssertionsMixin;
use Filament\PluginServiceProvider;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentServiceProvider extends PackageServiceProvider
{
//    protected array $resources = [
//        OrderResource::class,
//        CustomerResource::class,
//    ];

//    public function getResources(): array
//    {
//        return array_merge(
//            config('laravel-cart.filament'),
//            [
//                'order' => OrderResource::class,
//                'customer' => CustomerResource::class,
//                'adjustment' => AdjustmentResource::class
//            ]
//        );
//    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-cart-filament')
            ->hasViews('laravel-cart-filament');
    }

    public function boot()
    {
        parent::boot();

        //TestableLivewire::mixin(new FilamentAssertionsMixin());
        Testable::mixin(new FilamentAssertionsMixin());
    }
}
