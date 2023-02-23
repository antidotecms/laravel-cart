<?php

use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrder;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderLogItem;

it('has an order log item', function() {

    $product = TestProduct::factory()
        ->asSimpleProduct(['price' => 1000])
        ->create();

    $order = TestOrder::factory()->withProduct($product)->create();

    TestOrderLogItem::create([
        'message' => 'test log item',
        'test_order_id' => $order->id
    ]);

    expect($order->logItems()->count())->toBe(1);
    expect($order->logItems()->first()->message)->toBe('test log item');
});
