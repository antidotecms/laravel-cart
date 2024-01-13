<?php

it('will_update_the_order_status', function() {

    (new \Antidote\LaravelCartStripe\Testing\MockStripeHttpClient());

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $order = \Antidote\LaravelCart\Models\Order::create([
        'customer_id' => $customer->id
    ]);

    $payment = \Antidote\LaravelCart\Models\Payment::make([
        'payment_method_type' => \Antidote\LaravelCart\Enums\PaymentMethod::Stripe
    ]);

    $order->payment()->save($payment);

    $order->payment->data()->create([
        'key' => 'payment_intent_id',
        'value' => 'a payment intent id'
    ]);

    \Antidote\LaravelCart\Models\OrderItem::factory()
        ->withProduct(\Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct([
            'price' => 1000
        ])->create())
        ->forOrder($order)
        ->create();

    $payment = \Antidote\LaravelCart\Models\Payment::make([
        'payment_method_type' => \Antidote\LaravelCart\Enums\PaymentMethod::Stripe
    ]);

    $order->payment()->save($payment);

    app(\Antidote\LaravelCart\Domain\Cart::class)->setActiveOrder($order);

    $mock = \Mockery::mock(\Antidote\LaravelCartStripe\PaymentManager\StripePaymentManager::class)->makePartial();
    $mock->shouldReceive('updateStatus')->andReturnNull();
    $this->app->instance(\Antidote\LaravelCart\Models\Order::class, $mock);

    $this->withoutExceptionHandling();

    $response = $this->actingAs($customer, 'customer')
        ->get(\Antidote\LaravelCartFilament\CartPanelPlugin::get('urls.postCheckout'))
        ->assertSuccessful();
})
->covers(\Antidote\LaravelCart\Http\Controllers\PostCheckoutController::class);
