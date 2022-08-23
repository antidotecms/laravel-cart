<?php

namespace Tests\Fixtures\app\Models\ProductTypes;

use Antidote\LaravelCart\Concerns\ProductDataTypes\IsProductDataType;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
class SimpleProductDataType extends Model
{
    use IsProductDataType;

    protected $fillable = [
        'name',
        'price',
        'description'
    ];
}
