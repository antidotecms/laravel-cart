<?php

namespace Tests\Fixtures\app\Models\ProductTypes;

use Antidote\LaravelCart\Concerns\ProductDataTypes\IsProductDataType;
use Antidote\LaravelCart\Contracts\ProductDataType;
use Illuminate\Database\Eloquent\Model;

class ComplexProductDataType extends Model implements ProductDataType
{
    use IsProductDataType;

    protected $fillable = [
        'name',
        'width',
        'height'
    ];

    public function getPrice(...$args) : int
    {
        return $this->width * $this->height;
    }
}
