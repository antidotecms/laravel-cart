<?php

namespace Antidote\LaravelCart\Tests\laravel\app\Models;

use Antidote\LaravelCart\Contracts\Order;
use Antidote\LaravelCart\Tests\laravel\database\factories\TestOrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestOrder extends Order
{
    use SoftDeletes;
    use HasFactory;

    protected static function newFactory()
    {
        return TestOrderFactory::new();
    }

    public function updateStatus()
    {
        return null;
    }
}
