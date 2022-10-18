<?php

namespace Antidote\LaravelCart\Tests\laravel\app\Models;

use Antidote\LaravelCart\Tests\laravel\database\factories\TestStripeOrderFactory;
use Antidote\LaravelCartStripe\Models\StripeOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestStripeOrder extends StripeOrder
{
    use SoftDeletes;
    use HasFactory;

    protected static function newFactory()
    {
        return TestStripeOrderFactory::new();
    }
}
