<?php

namespace Tests\Fixtures\app\Models\Products;

use Antidote\LaravelCart\Concerns\IsProduct;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 * @property $name
 * @property $description
 * @property $price
 * @property $productDataType
 */
class Product extends Model implements \Antidote\LaravelCart\Contracts\Product
{
    use IsProduct;
}
