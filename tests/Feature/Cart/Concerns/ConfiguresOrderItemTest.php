<?php

it('sets up the corect fillable properties', function() {

    $adj = \Antidote\LaravelCart\Models\OrderItem::make();
    expect($adj->getFillable())->toEqualCanonicalizing([
        'name',
        'product_id',
        'product_data',
        'price',
        'quantity',
        'order_id',
    ]);
})
    ->covers(\Antidote\LaravelCart\Models\Concerns\ConfiguresOrderItem::class);

it('sets up the correct casts', function () {

    $adj = new \Antidote\LaravelCart\Models\OrderItem();
    expect($adj->getCasts())->toEqualCanonicalizing([
        'id' => 'int',
        'product_data' => 'array'
    ]);
})
->covers(\Antidote\LaravelCart\Models\Concerns\ConfiguresOrderItem::class);
