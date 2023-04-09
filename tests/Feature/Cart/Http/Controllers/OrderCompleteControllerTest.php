<?php

namespace Antidote\LaravelCart\Tests\Feature\Cart\Http\Controllers;

use Antidote\LaravelCart\Models\Adjustment;
use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Models\OrderAdjustment;
use Antidote\LaravelCart\Models\OrderItem;
use Antidote\LaravelCart\Models\OrderLogItem;
use Antidote\LaravelCart\ServiceProvider;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestPayment;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase;

/**
 * @covers \Antidote\LaravelCart\Http\Controllers\OrderCompleteController
 */
class OrderCompleteControllerTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithViews;
    //use WithoutMiddleware;

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../../../Fixtures/Cart/migrations');
    }

    public function defineEnv($app)
    {
        $app->config->set('laravel-cart.classes.order', Order::class);
        $app->config->set('laravel-cart.classes.customer', Customer::class);
        $app->config->set('laravel-cart.classes.payment', TestPayment::class);
        $app->config->set('laravel-cart.classes.product', TestProduct::class);
        $app->config->set('laravel-cart.classes.order_item', OrderItem::class);
        $app->config->set('laravel-cart.classes.order_log_item', OrderLogItem::class);
        $app->config->set('laravel-cart.classes.adjustment', Adjustment::class);
        $app->config->set('laravel-cart.classes.order_adjustment', OrderAdjustment::class);

        $app->config->set('laravel-cart.urls.order_complete', '/order-complete');
        $app->config->set('laravel-cart.views.order_complete', 'laravel-cart::order-complete');

        $app->config->set('auth.guards.customer', [
            'driver' => 'session',
            'provider' => 'test_customer'
        ]);

        $app->config->set('auth.providers.test_customer', [
            'driver' => 'eloquent',
            'model' => Customer::class
        ]);
    }

//    public function defineBindings($app)
//    {
//        $app->bind(
//            Order::class,
//            function() {
//                if($order_id = request()->get('order_id')) {
//                    $order = getClassNameFor('order')::where('id', $order_id)->first()->load('items.product.productType');
//                    if($order->customer->id == auth()->guard('customer')->user()->id) {
//                        return $order;
//                    }
//                }
//            }
//        );
//    }

    protected function defineRoutes($router)
    {
        $router->get('/login', null)->name('login');
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
            LivewireServiceProvider::class
        ];
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function it_will_update_the_order_status()
    {
//        $mock_order = $this->instance(Order::class, $this->partialMock(Order::class, function (\Mockery\MockInterface $mock_order) {
//            $mock_order->shouldReceive('updateStatus')->once();
//        }));

        $customer = Customer::factory()->create();

        $order = new class extends Order {
            public function updateStatus()
            {
                return null;
            }
        };

        $order = $order::create([
            'customer_id' => $customer->id
        ]);

        Config::set('laravel-cart.classes.order', $order::class);

        OrderItem::factory()
            ->withProduct(TestProduct::factory()->asSimpleProduct([
                'price' => 1000
            ])->create())
            ->forOrder($order)
            ->create();

        $mock = \Mockery::mock(Order::class)->makePartial();
        $mock->shouldReceive('updateStatus')->andReturnNull();
        $this->app->instance(Order::class, $mock);

        $this->actingAs($customer, 'customer')
            ->get('/order-complete?order_id='.$order->id)
            ->assertSuccessful();
    }

    /**
     * @test
     * @define-env defineEnv
     */
    //@todo this fails I think because Order is abstract and not an interface so unable to bind correctly via the serveice provider. Need to sort this!!
    public function it_will_not_allow_access_to_an_order_if_the_logged_in_user_is_not_the_customer()
    {
        $customer = Customer::factory()->create();
        $second_customer = Customer::factory()->create();

        $order = new class extends Order {
            public function updateStatus()
            {
                return null;
            }
        };

        \Config::set('laravel-cart.classes.order', $order::class);

//        $order = Order::factory()
//            ->forCustomer($customer)
//            ->create();

        $order = $order::create([
            'customer_id' => $customer->id
        ]);

        OrderItem::factory()
            ->withProduct(TestProduct::factory()->asSimpleProduct([
                'price' => 1000
            ])->create())
            ->forOrder($order)
            ->create();

        $this->actingAs($customer, 'customer')
            ->get('/order-complete?order_id='.$order->id)
            ->assertSuccessful();

        //needed if consecntuve http requests as the service provider is not called again
        //@link https://stackoverflow.com/questions/28425830/multiple-http-requests-in-laravel-5-integration-tests
        $this->refreshApplication();
        $this->refreshInMemoryDatabase();
        $this->setUpApplicationRoutes();

        $this->actingAs($second_customer, 'customer')
            ->get('/order-complete?order_id='.$order->id)
            ->assertNotFound();


    }

    /**
     * @test
     * @define-env defineEnv
     */
    function show_404_if_the_order_does_not_exist()
    {
        $this->actingAs(Customer::factory()->create(), 'customer')
            ->get('/order-complete?order_id=1')
            ->assertNotFound();
    }

    /**
     * @test
     * @define-env defineEnv
     * @define-env defineBindings
     * @todo do we need this? This essentially checks the auth middleware - we dont even need to crate classes as the service provider is not even hit.
     */
    public function redirect_to_login_if_the_user_is_not_logged_in()
    {
        $customer = Customer::factory()->create();

        $order = Order::factory()
            ->forCustomer($customer)
            ->create();

        OrderItem::factory()
            ->withProduct(TestProduct::factory()->asSimpleProduct([
                'price' => 1000
            ])->create())
            ->forOrder($order)
            ->create();

        $this->withMiddleware();

        $this->get('/order-complete?order_id='.$order->id)
            ->assertRedirect(route('login'));
    }

    /**
     * @test
     * @define-env defineEnv
     */
    function it_will_display_the_view_with_the_correct_data()
    {

        $customer = Customer::factory()->create();

        $order = new class extends Order {
            public function updateStatus()
            {
                return null;
            }
        };

        $order = $order::create([
            'customer_id' => $customer->id
        ]);

        Config::set('laravel-cart.classes.order', $order::class);

//        $order = Order::factory()
//            ->forCustomer($customer)
//            ->create();

        OrderItem::factory()
            ->withProduct(TestProduct::factory()->asSimpleProduct([
                'price' => 1000
            ])->create())
            ->forOrder($order)
            ->create();

        $this->actingAs($customer, 'customer')
            ->get('/order-complete?order_id='.$order->id)
            ->assertSuccessful()
            ->assertViewIs(config('laravel-cart.views.order_complete'))
            ->assertViewHas([
                'order' => $order,
                'completed' => false
            ]);
    }
}
