<?php

it('has an order', function() {

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct()->create();

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->create();

    $order_item = \Antidote\LaravelCart\Models\OrderItem::factory()
        ->withProduct($product)
        ->forOrder($order)
        ->create();

    $order_completed_event = new \Antidote\LaravelCart\Events\OrderCompleted($order);

    expect($order_completed_event->order->id)->toBe($order->id);

});
