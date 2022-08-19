<?php

namespace Tests\Fixtures\app\Models;

use Antidote\LaravelCart\Concerns\IsVariableProduct;
use Illuminate\Database\Eloquent\Model;

class VariableProduct extends Model implements \Antidote\LaravelCart\Contracts\VariableProduct
{
    use IsVariableProduct;

    protected $fillable = [
        'name'
    ];

    public function getName(array $specification) : string
    {
        return "{$this->name} with width of {$specification['width']} and height of {$specification['height']}";
    }

    public function getPrice(array $specification) : int
    {
        return $specification['width'] * $specification['height'];
    }
}
