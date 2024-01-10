<?php

it('it will not allow access to an order if the logged in user is not the customer', function () {

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();
    $second_customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $order = \Antidote\LaravelCart\Models\Order::create([
        'customer_id' => $customer->id
    ]);

    $payment = \Antidote\LaravelCart\Models\Payment::make([
        'payment_method_type' => \Antidote\LaravelCart\Enums\PaymentMethod::Stripe
    ]);

    $order->payment()->save($payment);

    \Antidote\LaravelCart\Models\OrderItem::factory()
        ->withProduct(\Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct([
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
})
->covers(\Antidote\LaravelCart\Http\Middleware\EnsureOrderBelongsToCustomer::class);

it('will show a 404 if the order does not exist', function () {

    $this->actingAs(\Antidote\LaravelCart\Models\Customer::factory()->create(), 'customer')
        ->get('/order-complete?order_id=1')
        ->assertNotFound();

})
->covers(\Antidote\LaravelCart\Http\Middleware\EnsureOrderBelongsToCustomer::class);
