<?php

namespace Antidote\LaravelCart\Tests\laravel\app\Models\ProductTypes;

use Antidote\LaravelCart\Contracts\ProductType;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
class SimpleProductDataType extends ProductType
{
    protected $fillable = [
        'name',
        'price',
        'description'
    ];

    public function getName(...$args) : string {
        return 'A Simple Product';
    }

    public function getPrice(...$args) : int {
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
