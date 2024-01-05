<?php

it('adds fillable fields', function () {

    //$adj = \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrder::factory()->create();
    $order = \Antidote\LaravelCart\Models\Order::factory()->create();
    expect($order->getFillable())->toEqual([
        'customer_id'
    ]);
})
->covers(\Antidote\LaravelCart\Models\Concerns\ConfiguresOrder::class);
