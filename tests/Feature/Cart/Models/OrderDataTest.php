<?php

use Antidote\LaravelCart\Models\Order;

it('can store and retrieve data', function () {

    $order = Order::factory()->create();

    $order->setData('note', 'Please leave at the back of the house');

    expect($order->getData('note'))->toBe('Please leave at the back of the house');

});

it('will return null if no data exists for key', function () {

    $order = Order::factory()->create();

    expect($order->getData('some_data'))->toBeNull();
});

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
});
