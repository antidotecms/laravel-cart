<?php

namespace Tests\Fixtures\app\Models;

use Antidote\LaravelCart\Concerns\IsProduct;
use Illuminate\Database\Eloquent\Model;

class VariableProduct extends Model implements \Antidote\LaravelCart\Contracts\VariableProduct
{
    use IsProduct;

    protected $fillable = [
        'name'
    ];

    public function getPrice(array $specification) : int
    {
        return $specification['width'] * $specification['height'];
    }
}
