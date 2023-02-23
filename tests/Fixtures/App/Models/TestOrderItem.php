<?php

namespace Antidote\LaravelCart\Tests\Fixtures\App\Models;

use Antidote\LaravelCart\Contracts\OrderItem;
use Antidote\LaravelCart\Tests\Fixtures\factories\TestOrderItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestOrderItem extends OrderItem
{
    use HasFactory;

    protected static function newFactory()
    {
        return TestOrderItemFactory::new();
    }
}
