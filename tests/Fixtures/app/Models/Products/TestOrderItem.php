<?php

namespace Tests\Fixtures\app\Models\Products;

class TestOrderItem extends \Antidote\LaravelCart\Contracts\OrderItem
{
    protected $casts = [
        'product_data' => 'array'
    ];
}
