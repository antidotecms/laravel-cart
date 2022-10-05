<?php

use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrder;

it('will record that a payment intent has been created', function() {

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();
    $order = TestOrder::factory()
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    expect($order->getTotal())->toBe($product->getPrice());

    $event = createStripeEvent('payment_intent.created', ['data' => ['object' => ['metadata' => ['order_id' => $order->id]]]]);

    $this->mock(\Stripe\Webhook::class, function(\Mockery\MockInterface $mock) use ($event) {
        $mock->shouldReceive('constructEvent')
            ->andReturn($event);
    });

    $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);

    expect($order->logItems()->count())->toBe(1);

});
