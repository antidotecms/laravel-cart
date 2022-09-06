<?php

namespace Tests\Fixtures\app\Models\ProductTypes;

use Antidote\LaravelCart\Concerns\ProductDataTypes\IsProductDataType;
use Antidote\LaravelCart\Contracts\ProductDataType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariableProductDataType extends Model implements ProductDataType
{
    use SoftDeletes;
    use IsProductDataType;

    protected $fillable = [
        'name'
    ];

    public function getName(?array $product_data = null) : string
    {
        return $product_data ?
            "{$this->name} with width of {$product_data['width']} and height of {$product_data['height']}" :
            $this->name;
    }

    public function getDescription(?array $product_data  = null): string
    {
        return "width: {$product_data['width']}, height: {$product_data['height']}";
    }

    public function getPrice(?array $product_data = null) : int
    {
        return $product_data ?
            $product_data['width'] * $product_data['height'] :
            120;
    }
}
