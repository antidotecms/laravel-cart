<?php

use Antidote\LaravelCart\Events\OrderCompleted;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

it('will record that a payment intent has been created', function() {

    Config::set('laravel-cart.classes.order', TestStripeOrder::class);
    Config::set('laravel-cart.classes.order_log_item', \Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrderLogItem::class);
    Config::set('laravel-cart.stripe.log', true);

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();
    $order = TestStripeOrder::factory()
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    expect($order->total)->toBe($product->getPrice() + (int)($product->getPrice() * config('laravel-cart.tax_rate')));

    $event = createStripeEvent('payment_intent.created', ['data' => ['object' => ['metadata' => ['order_id' => $order->id], 'id' => 'payment_intent_identifier']]]);

    $this->mock(\Stripe\Webhook::class, function(\Mockery\MockInterface $mock) use ($event) {
        $mock->shouldReceive('constructEvent')
            ->andReturn($event);
    });

    Log::shouldReceive('info')
        ->with(json_encode($event))
        ->once();

    $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);

    $order->refresh();

    expect($order->logItems()->count())->toBe(1);

    expect($order->status)->toBe('Payment Intent Created');
    expect($order->payment_intent_id)->toBe('payment_intent_identifier');

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

it('will record an unknown event', function () {

    Config::set('laravel-cart.classes.order', TestStripeOrder::class);
    Config::set('laravel-cart.classes.order_log_item', \Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrderLogItem::class);

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();
    $order = TestStripeOrder::factory()
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    expect($order->total)->toBe($product->getPrice() + (int)($product->getPrice() * config('laravel-cart.tax_rate')));

    $event = createStripeEvent('unknown_event', ['data' => ['object' => ['metadata' => ['order_id' => $order->id]]]]);

    $this->mock(\Stripe\Webhook::class, function(\Mockery\MockInterface $mock) use ($event) {
        $mock->shouldReceive('constructEvent')
            ->andReturn($event);
    });

    $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);

    $order->refresh();

    expect($order->logItems()->count())->toBe(1);
});

it('will log an event to the regular log file', function () {

    Config::set('laravel-cart.classes.order', TestStripeOrder::class);
    Config::set('laravel-cart.classes.order_log_item', \Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrderLogItem::class);
    Config::set('laravel-cart.stripe.log', true);

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();
    $order = TestStripeOrder::factory()
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    $event = createStripeEvent('payment_intent.created', ['data' => ['object' => ['metadata' => ['order_id' => 1]]]]);


    Log::shouldReceive('info')
        ->with(json_encode($event))
        ->once();

    $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);
});

it('will log an event to the specified channel', function () {

    Config::set('laravel-cart.classes.order', TestStripeOrder::class);
    Config::set('laravel-cart.classes.order_log_item', \Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrderLogItem::class);
    Config::set('laravel-cart.stripe.log', 'some_channel');

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();
    $order = TestStripeOrder::factory()
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    $event = createStripeEvent('payment_intent.created', ['data' => ['object' => ['metadata' => ['order_id' => 1]]]]);

    //@see https://stackoverflow.com/a/23807415/1424591
    Log::shouldReceive('channel')
        ->with('some_channel')
        ->once()
        ->andReturnSelf()
        ->shouldReceive('info')
        ->with(json_encode($event))
        ->once();

    $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);
});

it('will not log items', function () {

    Config::set('laravel-cart.classes.order', TestStripeOrder::class);
    Config::set('laravel-cart.classes.order_log_item', \Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrderLogItem::class);
    Config::set('laravel-cart.stripe.log', false);

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();
    $order = TestStripeOrder::factory()
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    $event = createStripeEvent('payment_intent.created', ['data' => ['object' => ['metadata' => ['order_id' => 1]]]]);

    //@see https://docs.mockery.io/en/latest/reference/expectations.html
    Log::shouldReceive('info')
        ->never();

    Log::shouldReceive('channel')
        ->never();

    $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);
});

it('will generate an OrderCompleted event', function () {

    Config::set('laravel-cart.classes.order', TestStripeOrder::class);
    Config::set('laravel-cart.classes.order_log_item', \Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrderLogItem::class);
    Config::set('laravel-cart.stripe.log', false);

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

    Event::fake();

    $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);

    Event::assertDispatched(OrderCompleted::class);
});
