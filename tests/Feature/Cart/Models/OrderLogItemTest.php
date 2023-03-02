<?php

use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;

it('has an order log item', function() {

    $product = TestProduct::factory()
        ->asSimpleProduct(['price' => 1000])
        ->create();

    $order = \Antidote\LaravelCart\Models\Order::factory()->withProduct($product)->create();

    \Antidote\LaravelCart\Models\OrderLogItem::create([
        'message' => 'test log item',
        'order_id' => $order->id
    ]);

    expect($order->logItems()->count())->toBe(1);
    expect($order->logItems()->first()->message)->toBe('test log item');
});
