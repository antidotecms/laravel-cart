<?php

it('sets up the corect fillable properties', function() {

    $adj = \Antidote\LaravelCart\Models\OrderAdjustment::make();
    expect($adj->getFillable())->toEqualCanonicalizing([
        'name',
        'order_id',
        'amount',
        'original_parameters',
        'class',
        'apply_to_subtotal'
    ]);
})
->covers(\Antidote\LaravelCart\Concerns\ConfiguresOrderAdjustment::class);

it('sets up the correct casts', function () {

    $adj = new \Antidote\LaravelCart\Models\OrderAdjustment();
    expect($adj->getCasts())->toEqualCanonicalizing([
        'id' => 'int',
        'original_parameters' => 'array'
    ]);
})
->covers(\Antidote\LaravelCart\Concerns\ConfiguresOrderAdjustment::class);
