<?php

namespace Tests\Fixtures\app\Models;

use Antidote\LaravelCart\Concerns\IsProduct;
use Antidote\LaravelCart\Contracts\Product;
use Illuminate\Database\Eloquent\Model;

class ComplexProduct extends Model implements Product
{
    use IsProduct;

    protected $fillable = [
        'name',
        'width',
        'height'
    ];

    public function getPrice() : int
    {
        return $this->width * $this->height;
    }
}
