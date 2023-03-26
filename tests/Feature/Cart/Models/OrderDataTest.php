<?php

use Antidote\LaravelCart\Models\Order;

it('can store and retrieve data', function () {

    $order = Order::factory()->create();

    $order->setData('note', 'Please leave at the back of the house');

    expect($order->getData('note'))->toBe('Please leave at the back of the house');

})
->coversClass(\Antidote\LaravelCart\Models\OrderData::class);

it('will return null if no data exists for key', function () {

    $order = Order::factory()->create();

    expect($order->getData('some_data'))->toBeNull();
})
->coversClass(\Antidote\LaravelCart\Models\OrderData::class);

it('can store and retrieve arrays as data', function () {

    $order = Order::factory()->create();

    $order->setData('specs', [
        'colour' => 'red',
        'size' => 'XL'
    ]);

    expect($order->getData('specs'))->toBe([
        'colour' => 'red',
        'size' => 'XL'
    ]);
})
->coversClass(\Antidote\LaravelCart\Models\OrderData::class);

it('will overwrite data with the same key', function () {

    $order = Order::factory()->create();

    $order->setData('some_data', 'a value');

    expect($order->data()->where('key', 'some_data')->count())->toBe(1);
    expect($order->getData('some_data'))->toBe('a value');

    $order->setData('some_data', 'a new value');

    expect($order->data()->where('key', 'some_data')->count())->toBe(1);
    expect($order->getData('some_data'))->toBe('a new value');
})
->coversClass(\Antidote\LaravelCart\Models\OrderData::class);
