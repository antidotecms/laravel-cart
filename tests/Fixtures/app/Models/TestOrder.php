<?php

namespace Tests\Fixtures\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestOrder extends \Antidote\LaravelCart\Contracts\Order
{
    use SoftDeletes;
    use HasFactory;
}
