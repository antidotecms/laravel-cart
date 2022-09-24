<?php

namespace Antidote\LaravelCart\Tests\Fixtures\cart\Models;

use Antidote\LaravelCart\Tests\Fixtures\database\factories\TestOrderItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestOrderItem extends \Antidote\LaravelCart\Contracts\OrderItem
{
    use HasFactory;

    protected static function newFactory()
    {
        return TestOrderItemFactory::new();
    }
}
