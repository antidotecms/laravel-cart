<?php

namespace Antidote\LaravelCart\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

abstract class ProductType extends Model
{
    public function product(): MorphOne
    {
        return $this->morphOne(config('laravel-cart.product_class'), 'product_type')->withTrashed();
    }

    public abstract function isValid(?array $product_data = null): bool;

    public static function booted()
    {
        static::deleting(function($product_type) {

            if(is_null($product_type->product) || $product_type->product->trashed())
            {
                return true;
            }

            if(!$product_type->product->trashed())
            {
                return false;
            }

        });
    }
}
