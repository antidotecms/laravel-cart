<?php

it('automatically populates the fillable fields', function () {

    Config::set('laravel-cart.order_class', \Tests\Fixtures\app\Models\TestOrder::class);
    $test_order_item = new \Tests\Fixtures\app\Models\TestOrderAdjustment;
    expect($test_order_item->getFillable())->toBe([
        'name',
        'class',
        'parameters',
        'test_order_id'
    ]);

    class NewOrder extends \Antidote\LaravelCart\Contracts\Order {};
    Config::set('laravel-cart.order_class', NewOrder::class);
    $new_order_adjustment = new class extends \Antidote\LaravelCart\Contracts\OrderAdjustment {};
    expect($new_order_adjustment->getFillable())->toBe([
        'name',
        'class',
        'parameters',
        'new_order_id'
    ]);

});

it('populates the casts', function () {

    $test_order_adjustment = new \Tests\Fixtures\app\Models\TestOrderAdjustment;

    expect($test_order_adjustment->getCasts())->toHaveKey('parameters');
    expect($test_order_adjustment->getCasts()['parameters'])->toBe('array');
});
