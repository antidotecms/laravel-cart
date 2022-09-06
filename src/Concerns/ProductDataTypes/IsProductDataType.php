<?php

namespace Antidote\LaravelCart\Concerns\ProductDataTypes;

use Exception;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait IsProductDataType
{
    public function product(): MorphOne
    {
        return $this->morphOne(config('laravel-cart.product_class'), 'product_data_type');
    }

    /**
     * @throws Exception
     */
    public function getName(?array $product_data = null): string
    {
        if (isset($this->name)) return $this->name;

        throw new Exception('Attribute `name` not on model. Please override `getName()`');
    }

    /**
     * @throws Exception
     */
    public function getDescription(?array $product_data = null): string
    {
        if(isset($this->description)) return $this->description;

        throw new Exception('Attribute `description` not on model. Please override `getDescription()`');
    }

    /**
     * @throws Exception
     */
    public function getPrice(?array $product_data = null): int
    {
        if(isset($this->price)) return $this->price;

        throw new Exception('Attribute `price` not on model. Please override `getPrice()`');
    }

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
