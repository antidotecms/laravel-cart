<?php

namespace Tests\Fixtures\app\Models\ProductTypes;

use Antidote\LaravelCart\Contracts\ProductType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin Model
 */
class VariableProductDataType extends ProductType
{
    use SoftDeletes;

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
