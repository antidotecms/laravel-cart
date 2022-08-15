<?php

namespace Tests\Fixtures\app\Models;

use Antidote\LaravelCart\Concerns\IsProduct;
use Antidote\LaravelCart\Contracts\Product;
use Illuminate\Database\Eloquent\Model;

class SimpleProduct extends Model implements Product
{
    use IsProduct;

    protected $fillable = [
        'name',
        'price'
    ];
}
