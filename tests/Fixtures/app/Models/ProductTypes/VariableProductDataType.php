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

    public function getName(array $product_data) : string
    {
        return "A Variable Product with width of {$product_data['width']} and height of {$product_data['height']}";
    }

    public function getDescription(array $product_data): string
    {
        return "width: {$product_data['width']}, height: {$product_data['height']}";
    }

    public function getPrice(array $product_data) : int
    {
        return $product_data['width'] * $product_data['height'];
    }

    public function isValid(?array $product_data = null): bool
    {
        return true;
    }
}
