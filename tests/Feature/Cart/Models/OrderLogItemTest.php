<?php

use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;

it('belongs to an order', function() {

    $product = TestProduct::factory()
        ->asSimpleProduct(['price' => 1000])
        ->create();

    $order = \Antidote\LaravelCart\Models\Order::factory()->withProduct($product)->create();

    $order_log_item = \Antidote\LaravelCart\Models\OrderLogItem::create([
        'message' => 'test log item',
        'order_id' => $order->id
    ]);

    expect($order_log_item->order)->not()->toBeNull();
    expect($order_log_item->order->id)->toBe($order->id);
})
->coversClass(\Antidote\LaravelCart\Models\OrderLogItem::class);
