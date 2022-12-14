<?php

namespace Antidote\LaravelCart\Tests;

use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrder;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrderAdjustment;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrderItem;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrderLogItem;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestPayment;
use Antidote\LaravelCartStripe\Http\Controllers\StripeWebhookController;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Livewire\LivewireServiceProvider;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected function migrateUsing()
    {
        return [
            '--path' => [
                './database/migrations/cart'
            ]
        ];
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/laravel/database/migrations');
    }

    protected function defineRoutes($router)
    {
        $router->post(config('laravel-cart.urls.stripe.webhook_handler'), StripeWebhookController::class);
    }

    protected function getPackageProviders($app): array
    {
        return [
            'Antidote\LaravelCart\ServiceProvider',
            'Antidote\LaravelCartStripe\ServiceProvider',
            'Antidote\LaravelCartFilament\ServiceProvider',
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
        ];
    }

    protected function getBasePath()
    {
        return __DIR__.'/laravel';
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

        Config::set('laravel-cart.classes.product', TestProduct::class);
        Config::set('laravel-cart.classes.customer', TestCustomer::class);
        Config::set('laravel-cart.classes.order', TestOrder::class);
        Config::set('laravel-cart.classes.order_item', TestOrderItem::class);
        Config::set('laravel-cart.classes.order_adjustment', TestOrderAdjustment::class);
        Config::set('laravel-cart.classes.payment', TestPayment::class);
        Config::set('laravel-cart.classes.order_log_item', TestOrderLogItem::class);
        Config::set('laravel-cart.tax_rate', 0.2);

        Config::set('laravel-cart.stripe.secret_key', 'secret_key');
    }
}
