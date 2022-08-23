<?php

namespace Tests\Fixtures\app\Models\ProductTypes;

use Antidote\LaravelCart\Concerns\ProductDataTypes\IsProductDataType;
use Antidote\LaravelCart\Contracts\ProductDataType;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
class SimpleProductDataType extends Model implements ProductDataType
{
    use IsProductDataType;

    protected $fillable = [
        'name',
        'price',
        'description'
    ];
}
