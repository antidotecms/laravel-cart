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

    public function getName(?array $specification = null) : string
    {
        return $specification ?
            "{$this->name} with width of {$specification['width']} and height of {$specification['height']}" :
            $this->name;
    }

    public function getPrice(?array $specification = null) : int
    {
        return $specification ?
            $specification['width'] * $specification['height'] :
            120;
    }
}
