<?php

namespace Antidote\LaravelCart\Tests;

use Antidote\LaravelCart\CartServiceProvider;
use Antidote\LaravelCart\Tests\Fixtures\Filament\TestPanelProviderWithCustomResources;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Livewire\LivewireServiceProvider;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

class CustomResourcesTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            //'Antidote\LaravelCart\ServiceProvider',
            CartServiceProvider::class,
            'Antidote\LaravelCartStripe\StripeServiceProvider',
            \Antidote\LaravelCartFilament\FilamentServiceProvider::class,
            ActionsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            //CartPanelPlugin::class
            TestPanelProviderWithCustomResources::class
        ];
    }
}
