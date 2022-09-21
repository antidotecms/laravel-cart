<?php

namespace Tests\Fixtures\app\Models\Products;

use Illuminate\Database\Eloquent\SoftDeletes;

class TestOrder extends \Antidote\LaravelCart\Models\Order
{
    use SoftDeletes;

    //@todo automatically populate this
    protected $fillable = [
        'test_customer_id'
    ];
}
