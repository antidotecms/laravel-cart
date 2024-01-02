<?php

it('adds fillable fields', function () {

    $product = \Antidote\LaravelCart\Models\Product::make();
    expect($product->getFillable())->toEqualCanonicalizing([
        'product_type_type',
        'product_type_id',
        'name'
    ]);
})
->covers(\Antidote\LaravelCart\Models\Concerns\ConfiguresProduct::class);
