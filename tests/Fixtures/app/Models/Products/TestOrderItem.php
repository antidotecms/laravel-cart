<?php

namespace Tests\Fixtures\app\Models\Products;

class TestOrderItem extends \Antidote\LaravelCart\Contracts\OrderItem
{
    protected $fillable = [
        'name',
        'test_product_id',
        'product_data',
        'price',
        'quantity'
    ];

    protected $casts = [
        'product_data' => 'array'
    ];
}
