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

    public function getName(...$args) : string {
        return 'A Simple Product';
    }

    public function getPrice(...$args) : string {
        return $this->price;
    }

    public function getDescription() : string {
        return $this->description;
    }

    public function isValid(?array $product_data = null): bool
    {
        return true;
    }
}
