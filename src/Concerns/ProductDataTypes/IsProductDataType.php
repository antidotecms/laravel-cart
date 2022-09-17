<?php

namespace Antidote\LaravelCart\Concerns\ProductDataTypes;

use Illuminate\Database\Eloquent\Relations\MorphOne;

trait IsProductDataType
{

    public function product(): MorphOne
    {
        return $this->morphOne(config('laravel-cart.product_class'), 'product_data_type')->withTrashed();
    }

    public abstract function isValid(?array $product_data = null): bool;

    public static function booted()
    {
        static::deleting(function($product_data_type) {

            if(is_null($product_data_type->product) || $product_data_type->product->trashed())
            {
                return true;
            }

            if(!$product_data_type->product->trashed())
            {
                return false;
            }

        });
    }

}
