<?php

use Tests\Fixtures\app\Models\Products\TestOrderItem;

it('automatically populates the fillable fields', function () {

    $test_order_item = new TestOrderItem;
    expect($test_order_item->getFillable())->toBe([
        'name',
        'test_product_id',
        'product_data',
        'price',
        'quantity'
    ]);

    class NewProduct extends \Antidote\LaravelCart\Contracts\Product {};
    Config::set('laravel-cart.product_class', NewProduct::class);
    $new_order_item = new class extends \Antidote\LaravelCart\Contracts\OrderItem {};
    expect($new_order_item->getFillable())->toBe([
        'name',
        'new_product_id',
        'product_data',
        'price',
        'quantity'
    ]);

});

it('populates the casts', function () {

    $test_order_item = new TestOrderItem;

    expect($test_order_item->getCasts())->toHaveKey('product_data');
    expect($test_order_item->getCasts()['product_data'])->toBe('array');
});
