<?php

namespace Tests\Fixtures\app\Models\ProductTypes;

use Antidote\LaravelCart\Contracts\ProductType;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
class ComplexProductDataType extends ProductType
{
    protected $fillable = [
        'width',
        'height'
    ];

    public function getPrice(?array $product_data = null) : int
    {
        return $this->width * $this->height;
    }

    public function getName(?array $product_data = null): string
    {
        return "{$this->width} x {$this->height} object";
    }

    public function isValid(?array $product_data = null): bool
    {
        //20 x 20 is an invalid product
        return !($this->width == 20 && $this->height == 20);
    }
}
