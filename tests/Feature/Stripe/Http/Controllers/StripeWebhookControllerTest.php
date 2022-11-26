<?php

use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrder;

it('will record that a payment intent has been created', function() {

    Config::set('laravel-cart.classes.order', TestStripeOrder::class);
    Config::set('laravel-cart.classes.order_log_item', \Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrderLogItem::class);

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();
    $order = TestStripeOrder::factory()
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    expect($order->total)->toBe($product->getPrice() + (int)($product->getPrice() * config('laravel-cart.tax_rate')));

    $event = createStripeEvent('payment_intent.created', ['data' => ['object' => ['metadata' => ['order_id' => $order->id]]]]);

    $this->mock(\Stripe\Webhook::class, function(\Mockery\MockInterface $mock) use ($event) {
        $mock->shouldReceive('constructEvent')
            ->andReturn($event);
    });

    $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);

    $order->refresh();

    expect($order->logItems()->count())->toBe(1);

    expect($order->status)->toBe('Payment Intent Created');

});

it('will record that a payment intent was successful', function () {

    Config::set('laravel-cart.classes.order', TestStripeOrder::class);
    Config::set('laravel-cart.classes.order_log_item', \Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrderLogItem::class);

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();
    $order = TestStripeOrder::factory()
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    expect($order->total)->toBe($product->getPrice() + (int)($product->getPrice() * config('laravel-cart.tax_rate')));

    $event = createStripeEvent('payment_intent.succeeded', ['data' => ['object' => ['metadata' => ['order_id' => $order->id]]]]);

    $this->mock(\Stripe\Webhook::class, function(\Mockery\MockInterface $mock) use ($event) {
        $mock->shouldReceive('constructEvent')
            ->andReturn($event);
    });

    $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);

    $order->refresh();

    expect($order->logItems()->count())->toBe(1);

    expect($order->status)->toBe('Payment Intent Succeeded');
});

it('will record that a charge was successful', function () {

    Config::set('laravel-cart.classes.order', TestStripeOrder::class);
    Config::set('laravel-cart.classes.order_log_item', \Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrderLogItem::class);

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();
    $order = TestStripeOrder::factory()
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    expect($order->total)->toBe($product->getPrice() + (int)($product->getPrice() * config('laravel-cart.tax_rate')));

    $event = createStripeEvent('charge.succeeded', ['data' => ['object' => ['metadata' => ['order_id' => $order->id]]]]);

    $this->mock(\Stripe\Webhook::class, function(\Mockery\MockInterface $mock) use ($event) {
        $mock->shouldReceive('constructEvent')
            ->andReturn($event);
    });

    $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);

    $order->refresh();

    expect($order->logItems()->count())->toBe(1);

    expect($order->status)->toBe('Charge Succeeded');
});

it('will record that a payment intent was cancelled', function () {

    Config::set('laravel-cart.classes.order', TestStripeOrder::class);
    Config::set('laravel-cart.classes.order_log_item', \Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrderLogItem::class);

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();
    $order = TestStripeOrder::factory()
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    expect($order->total)->toBe($product->getPrice() + (int)($product->getPrice() * config('laravel-cart.tax_rate')));

    $event = createStripeEvent('payment_intent.cancelled', ['data' => ['object' => ['metadata' => ['order_id' => $order->id]]]]);

    $this->mock(\Stripe\Webhook::class, function(\Mockery\MockInterface $mock) use ($event) {
        $mock->shouldReceive('constructEvent')
            ->andReturn($event);
    });

    $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);

    $order->refresh();

    expect($order->logItems()->count())->toBe(1);

    expect($order->status)->toBe('Payment Intent Canceled');
});

it('will record that a payment intent payment failed', function () {

    Config::set('laravel-cart.classes.order', TestStripeOrder::class);
    Config::set('laravel-cart.classes.order_log_item', \Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrderLogItem::class);

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();
    $order = TestStripeOrder::factory()
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    expect($order->total)->toBe($product->getPrice() + (int)($product->getPrice() * config('laravel-cart.tax_rate')));

    $event = createStripeEvent('payment_intent.payment_failed', ['data' => ['object' => ['metadata' => ['order_id' => $order->id]]]]);

    $this->mock(\Stripe\Webhook::class, function(\Mockery\MockInterface $mock) use ($event) {
        $mock->shouldReceive('constructEvent')
            ->andReturn($event);
    });

    $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);

    $order->refresh();

    expect($order->logItems()->count())->toBe(1);

    expect($order->status)->toBe('Payment Intent Payment Failed');
});
