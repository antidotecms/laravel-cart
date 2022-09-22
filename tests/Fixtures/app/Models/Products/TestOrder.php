<?php

namespace Tests\Fixtures\app\Models\Products;

use Illuminate\Database\Eloquent\SoftDeletes;

class TestOrder extends \Antidote\LaravelCart\Contracts\Order
{
    use SoftDeletes;
}
