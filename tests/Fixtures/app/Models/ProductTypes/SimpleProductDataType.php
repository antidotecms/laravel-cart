<?php

namespace Tests\Fixtures\app\Models\ProductTypes;

use Antidote\LaravelCart\Concerns\ProductDataTypes\IsProductDataType;
use Antidote\LaravelCart\Concerns\ProductDataTypes\IsSimpleProductDataType;
use Illuminate\Database\Eloquent\Model;

class SimpleProductDataType extends Model
{
    use IsProductDataType;

    protected $fillable = [
        'name',
        'price',
        'description'
    ];
}
