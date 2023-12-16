<?php

namespace Antidote\LaravelCart\Tests\Fixtures\App\Models\ProductTypes;

use Antidote\LaravelCart\Models\Products\SimpleProductType;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
class InvalidSimpleProductDataType extends SimpleProductType
{
    protected $table = 'simple_product_data_types';
    public function isValid(?array $product_data = null): bool
    {
        return false;
    }
}
