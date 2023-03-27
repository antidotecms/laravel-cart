<?php

it('belongs to an order', function() {

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->for(\Antidote\LaravelCart\Models\Customer::factory())
        ->create();

//    $order_data = \Antidote\LaravelCart\Models\OrderData::factory()
//        ->for($order)
//        ->create([
//            'key' => 'note',
//            'value' => 'some notes'
//        ]);

    $order_data = \Antidote\LaravelCart\Models\OrderData::factory()
        ->for($order)
        ->create([
            'key' => 'note',
            'value' => 'some notes'
        ]);

    expect($order_data->order->id)->toBe($order->id);
})
->coversClass(\Antidote\LaravelCart\Models\OrderData::class);


