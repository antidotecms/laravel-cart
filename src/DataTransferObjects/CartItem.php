<?php

namespace Antidote\LaravelCart\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;

class CartItem extends \Spatie\DataTransferObject\DataTransferObject implements Arrayable
{
    public int $product_id;
    public array | null $product_data;
    public int $quantity;

    public function getProduct()
    {
        //@todo why does this return incorrect model - return $this->product_type::find($this->product_id)->first();
        $product_class = config('laravel-cart.product_class');
        return $product_class::with('productDataType')->where('id', $this->product_id)->first();
    }
}
