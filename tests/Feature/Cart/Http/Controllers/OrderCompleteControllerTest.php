<?php

namespace Antidote\LaravelCart\Tests\Feature\Cart\Http\Controllers;

use Antidote\LaravelCart\Contracts\Order;
use Antidote\LaravelCart\ServiceProvider;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestAdjustment;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestCustomer;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrder;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderItem;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderLogItem;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestPayment;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Orchestra\Testbench\TestCase;

class OrderCompleteControllerTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithViews;
    use WithoutMiddleware;

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../../../Fixtures/Cart/migrations');
    }

    public function defineEnv($app)
    {
        $app->config->set('laravel-cart.classes.order', TestOrder::class);
        $app->config->set('laravel-cart.classes.customer', TestCustomer::class);
        $app->config->set('laravel-cart.classes.payment', TestPayment::class);
        $app->config->set('laravel-cart.classes.product', TestProduct::class);
        $app->config->set('laravel-cart.classes.order_item', TestOrderItem::class);
        $app->config->set('laravel-cart.classes.order_log_item', TestOrderLogItem::class);
        $app->config->set('laravel-cart.classes.adjustment', TestAdjustment::class);

        $app->config->set('laravel-cart.urls.order_complete', '/order-complete');
        $app->config->set('laravel-cart.views.order_complete', 'laravel-cart::order-complete');

        $app->config->set('auth.guards.customer', [
            'driver' => 'session',
            'provider' => 'test_customer'
        ]);

        $app->config->set('auth.providers.test_customer', [
            'driver' => 'eloquent',
            'model' => TestCustomer::class
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
            ServiceProvider::class
        ];
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function it_will_update_the_order_status()
    {
        $mock_order = $this->instance(TestOrder::class, $this->partialMock(TestOrder::class, function (\Mockery\MockInterface $mock_order) {
            $mock_order->shouldReceive('updateStatus')->once();
        }));

        $this->app->bind(
            Order::class,
//            function() {
//                if($order_id = request()->get('order_id')) {
//                    $order = getClassNameFor('order')::where('id', $order_id)->first()->load('items.product.productType');
//                    if($order->customer->id == auth()->guard('customer')->user()->id) {
//                        return $order;
//                    }
//                }
//            }
            fn() => $mock_order
        );

        $customer = TestCustomer::factory()->create();

        $order = TestOrder::factory()
            ->forCustomer($customer)
            ->create();

        TestOrderItem::factory()
            ->withProduct(TestProduct::factory()->asSimpleProduct([
                'price' => 1000
            ])->create())
            ->forOrder($order)
            ->create();

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
        $customer = TestCustomer::factory()->create();
        $second_customer = TestCustomer::factory()->create();
        $order = TestOrder::factory()
            ->forCustomer($customer)
            ->create();

        TestOrderItem::factory()
            ->withProduct(TestProduct::factory()->asSimpleProduct([
                'price' => 1000
            ])->create())
            ->forOrder($order)
            ->create();

        $this->actingAs($customer, 'customer')
            ->get('/order-complete?order_id='.$order->id)
            ->assertSuccessful();

        $this->actingAs($second_customer)
            ->get('/order-complete?order_id='.$order->id)
            ->assertNotFound();
    }

    /**
     * @test
     * @define-env defineEnv
     */
    function show_404_if_the_order_does_not_exist()
    {
        $this->actingAs(TestCustomer::factory()->create())
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
        $customer = TestCustomer::factory()->create();

        $order = TestOrder::factory()
            ->forCustomer($customer)
            ->create();

        TestOrderItem::factory()
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

        $customer = TestCustomer::factory()->create();

        $order = TestOrder::factory()
            ->forCustomer($customer)
            ->create();

        TestOrderItem::factory()
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