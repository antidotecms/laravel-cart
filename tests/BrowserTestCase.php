<?php

namespace Antidote\LaravelCart\Tests;

use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderAdjustment;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderLogItem;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestPayment;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Laravel\Dusk\DuskServiceProvider;
use Orchestra\Testbench\Dusk\Foundation\TestbenchServiceProvider;

//use WireUi\Providers\WireUiServiceProvider;

//use Filament\FilamentServiceProvider;
//use Filament\Forms\FormsServiceProvider;
//use Filament\Tables\TablesServiceProvider;
//use Livewire\LivewireServiceProvider;

class BrowserTestCase extends \Orchestra\Testbench\Dusk\TestCase
{
    use DatabaseTransactions;

    //protected static $baseServePort = 80;

    protected function defineDatabaseMigrations()
    {
        //$this->artisan('migrate:fresh');
        //$this->artisan('migrate:fresh', ['--database' => 'testbench'])->run();

//        $this->beforeApplicationDestroyed(function () {
//            $this->artisan('migrate:rollback', ['--database' => 'testbench'])->run();
//        });

        //$this->loadMigrationsFrom(__DIR__.'/laravel/database/migrations');
        //$this->loadLaravelMigrations(['--database' => 'testbench']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();

        $this->tweakApplication(function ($app, $config) {
            $app['router']->get('/checkout', fn() => view('checkout'));
            $app['router']->get('/', fn() => "baweep");
            $app['router']->get('/test', fn() => env('DB_CONNECTION'));
            $app['router']->get('/user', fn() => print_r(auth()->user()->attributesToArray(), true));
            $app['router']->get('/hello', fn() => "hello");
            //$app['router']->get('_dusk/login/{userId}/{guard?}', [UserController::class, 'login'])->name('login');
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
            DuskServiceProvider::class,
            \Orchestra\Testbench\Foundation\TestbenchServiceProvider::class,
            TestbenchServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => __DIR__.'/laravel/database/database.sqlite.example',
            'prefix'   => '',
        ]);

        //$app['config']->set('auth.password_timeout',10800);

        //$app['config']->set('auth.defaults.guard', 'web');
        //$app['config']->set('dusk.domain', 'localhost');
        $app['config']->set('auth.guards', [
            'web' => [
                'driver' => 'session',
                'provider' => 'test_customers',
            ]
        ]);
        $app['config']->set('auth.providers', [
            'test_customers' => [
                'driver' => 'eloquent',
                'model' => Customer::class,
            ]
        ]);
        //$app['config']->set('database.connections.testbench', 'mysql');

        //$app['config']->set('session.driver', 'file');

        Config::set('laravel-cart.classes.product', TestProduct::class);
        Config::set('laravel-cart.classes.customer', Customer::class);
        Config::set('laravel-cart.classes.order', Order::class);
        Config::set('laravel-cart.classes.order_item', OrderItem::class);
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

//    protected function getBasePath()
//    {
//        return __DIR__.'/laravel';
//    }

//    protected function driver() : RemoteWebDriver
//    {
//        $options = (new ChromeOptions())->addArguments([
//            //'--disable-gpu',
//            //'--headless',
//            //'--window-size=1920,1080',
//            '--no-sandbox',
//            '--enable-file-cookies',
//        ]);
//
//        // I use Docker, container name is chrome.
//        return RemoteWebDriver::create(
//            'http://127.0.0.1:9515',
//            DesiredCapabilities::chrome()->setCapability(
//                ChromeOptions::CAPABILITY,
//                $options
//            )
//        );
//    }
}
