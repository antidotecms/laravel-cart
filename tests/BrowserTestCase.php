<?php

namespace Antidote\LaravelCart\Tests;

use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrder;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrderAdjustment;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrderItem;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrderLogItem;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestPayment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;

//use WireUi\Providers\WireUiServiceProvider;

//use Filament\FilamentServiceProvider;
//use Filament\Forms\FormsServiceProvider;
//use Filament\Tables\TablesServiceProvider;
//use Livewire\LivewireServiceProvider;

class BrowserTestCase extends \Orchestra\Testbench\Dusk\TestCase
{
    use DatabaseTransactions;

    protected static $baseServePort = 80;

    protected function defineDatabaseMigrations()
    {
        $this->artisan('migrate:fresh');
        $this->loadMigrationsFrom(__DIR__.'/laravel/database/migrations');
        $this->loadLaravelMigrations(['--database' => 'testbench']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        //$this->loadLaravelMigrations();

        $this->tweakApplication(function ($app, $config) {
            $app['router']->get('/checkout', fn() => view('checkout'));
            $app['router']->get('/', fn() => "home");
            //$app['router']->get('/login', fn() => 'login')->name('dusk.login');
            //$app['router']->get('/user', fn() => 'user')->name('dusk.user');
        });
    }

    protected function getPackageProviders($app): array
    {
        return [
            'Antidote\LaravelCart\ServiceProvider',
            'Antidote\LaravelCartStripe\ServiceProvider',
//            LivewireServiceProvider::class,
//            FilamentServiceProvider::class
            //WireUiServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        //$this->loadFactoriesUsing($app, __DIR__.'/Fixtures/database/factories');

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => __DIR__.'/laravel/database/database.sqlite.example',
            'prefix'   => '',
        ]);
        //$app['config']->set('database.connections.testbench', 'mysql');

        $app['config']->set('session.driver', 'file');

        Config::set('laravel-cart.classes.product', TestProduct::class);
        Config::set('laravel-cart.classes.customer', TestCustomer::class);
        Config::set('laravel-cart.classes.order', TestOrder::class);
        Config::set('laravel-cart.classes.order_item', TestOrderItem::class);
        Config::set('laravel-cart.classes.order_adjustment', TestOrderAdjustment::class);
        Config::set('laravel-cart.classes.payment', TestPayment::class);
        Config::set('laravel-cart.classes.order_log_item', TestOrderLogItem::class);
        Config::set('laravel-cart.stripe.secret_key', 'not_a_real_key');

        //$app['config']->set('database.default', 'mysql');
    }

//    protected function defineEnvironment($app)
//    {
//        //$app['config']->set('app.debug', true);
//    }

    protected function getBasePath()
    {
        return __DIR__.'/laravel';
    }
}
