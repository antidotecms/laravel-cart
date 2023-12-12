<?php

it('adds fillable fields', function () {

    $adj = \Antidote\LaravelCart\Models\OrderLogItem::make();
    expect($adj->getFillable())->toEqual([
        'message',
        'order_id'
    ]);
})
->covers(\Antidote\LaravelCart\Concerns\ConfiguresOrderLogItem::class);
