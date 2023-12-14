<?php

uses(
    \Antidote\LaravelCart\Tests\OrderControllerTestCase::class,
    \Illuminate\Foundation\Testing\RefreshDatabase::class
);

use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Models\OrderItem;
use Antidote\LaravelCart\Tests\Feature\Cart\Http\Controllers\O;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrder;

it('will_update_the_order_status', function() {

    $customer = Customer::factory()->create();

    $order = TestOrder::create([
        'customer_id' => $customer->id
    ]);

    OrderItem::factory()
        ->withProduct(TestProduct::factory()->asSimpleProduct([
            'price' => 1000
        ])->create())
        ->forOrder($order)
        ->create();

    $mock = \Mockery::mock(TestOrder::class)->makePartial();
    $mock->shouldReceive('updateStatus')->andReturnNull();
    $this->app->instance(TestOrder::class, $mock);

    $this->withoutExceptionHandling();

    $response = $this->actingAs($customer, 'customer')
        ->get('/order-complete?order_id='.$order->id)
        ->assertSuccessful();
})
->covers(\Antidote\LaravelCart\Http\Controllers\OrderCompleteController::class);

it('it will not allow access to an order if the logged in user is not the customer', function () {

    $customer = Customer::factory()->create();
    $second_customer = Customer::factory()->create();

    $order = TestOrder::create([
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
    $this->setUpApplicationRoutes(app());

    $this->actingAs($second_customer, 'customer')
        ->get('/order-complete?order_id='.$order->id)
        ->assertNotFound();
});

it('will show a 404 if the order does not exist', function () {

    $this->actingAs(Customer::factory()->create(), 'customer')
        ->get('/order-complete?order_id=1')
        ->assertNotFound();

});

it('will redirect to login if the user is not logged in', function () {

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

});

it('will display the view with the correct data', function () {

    $customer = Customer::factory()->create();

    $order = TestOrder::create([
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
        ->assertSuccessful()
        ->assertViewIs(config('laravel-cart.views.order_complete'))
        ->assertViewHas([
            'order' => $order,
            'completed' => false
        ]);
});
