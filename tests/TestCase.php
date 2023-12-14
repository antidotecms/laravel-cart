<?php

namespace Antidote\LaravelCart\Tests;

use Antidote\LaravelCart\CartServiceProvider;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestPayment;
use Antidote\LaravelCart\Tests\Fixtures\Filament\TestPanelProvider;
use Antidote\LaravelCartStripe\StripeServiceProvider;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\LivewireServiceProvider;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/Fixtures/Cart/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            StripeServiceProvider::class,
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
            TestPanelProvider::class,
            CartServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app->config->set('laravel-cart.tax_rate', 0.2);

        $app->config->set('laravel-cart.stripe.secret_key', 'secret_key');

        $app->config->set('laravel-cart.views.order_complete', 'laravel-cart::order-complete');
    }

    protected function defineRoutes($router)
    {
        $router->get('/login', null)->name('login');
    }

}
