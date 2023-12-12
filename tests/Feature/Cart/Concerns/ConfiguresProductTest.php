<?php

it('adds fillable fields', function () {

    $product = \Antidote\LaravelCart\Models\Product::make();
    expect($product->getFillable())->toEqual([
        'product_type_type',
        'product_type_id'
    ]);
})
->covers(\Antidote\LaravelCart\Concerns\ConfiguresProduct::class);
