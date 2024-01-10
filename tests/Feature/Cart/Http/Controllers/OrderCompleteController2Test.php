<?php

uses(
    //\Antidote\LaravelCart\Tests\OrderControllerTestCase::class,
    \Illuminate\Foundation\Testing\RefreshDatabase::class
);

use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Models\OrderItem;
use Antidote\LaravelCart\Tests\Feature\Cart\Http\Controllers\O;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrder;


//@todo is this needed? Does thi sjus test that the middleware is in place which is now the responsibility of the developer
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

    $this->markTestSkipped('Developer now uses own page and uses a component from the package');
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
