<?php

//uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Cart/Database/Factories');
//uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Cart/Models');
//uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Cart/Http/Controllers');
//uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Cart/Commands');
//uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Cart/Mail');
//uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Filament');
//uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Stripe/Http/Middleware');
//uses(\Antidote\LaravelCart\Tests\TestCase::class)->in('Feature/Filament');
//uses(\Antidote\LaravelCart\Tests\StripeTestCase::class)->in('Feature/Stripe/Http/Controllers');
//uses(\Antidote\LaravelCart\Tests\BrowserTestCase::class)->in('Browser');
use Antidote\LaravelCart\CartServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Panel;
use Filament\Support\SupportServiceProvider;
use Livewire\LivewireServiceProvider;

uses(\Antidote\LaravelCart\Tests\TestCase::class)->in(
    'Feature/Cart',
    'Feature/Stripe',
);
uses(\Antidote\LaravelCart\Tests\StripeTestCase::class)->in('Feature/Filament');
uses(\Orchestra\Testbench\PHPUnit\TestCase::class)->in(
    \Tests\Feature\Filament\ServiceProviderWithCustomResourcesTest::class
);

//uses(RefreshDatabase::class)->in('Feature');

function actingAsCustomer(\Antidote\LaravelCart\Models\Customer $customer, string $driver = null)
{
    return test()->actingAs($customer, $driver);
}

function setUpCartPlugin(\Antidote\LaravelCartFilament\CartPanelPlugin $plugin)
{
    $filamentServiceProvider = new FilamentServiceProvider(app());
    $supportServiceprovider = new SupportServiceProvider(app());
    $livewireServiceProvider = new LivewireServiceProvider(app());
    $cartServiceProvider = new CartServiceProvider(app());

    app()->register($livewireServiceProvider);
    app()->register($filamentServiceProvider);
    app()->register($supportServiceprovider);

    $panel = new Panel();
    $panel->id('testing');
    $panel->plugin($plugin);
    app()->get('filament')->setCurrentPanel($panel);

    app()->register($cartServiceProvider);

    $filamentServiceProvider->boot();
    $cartServiceProvider->boot();
}




