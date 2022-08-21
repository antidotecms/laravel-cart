<?php

namespace Tests\Fixtures\app\Models\ProductTypes;

use Antidote\LaravelCart\Concerns\ProductDataTypes\IsProductDataType;
use Antidote\LaravelCart\Concerns\ProductDataTypes\IsSimpleProductDataType;
use Illuminate\Database\Eloquent\Model;

class ComplexProductDataType extends Model
{
    use IsProductDataType;

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
