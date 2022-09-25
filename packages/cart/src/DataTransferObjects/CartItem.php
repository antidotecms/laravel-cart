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
        return getClassNameFor('product')::with('productType')->find($this->product_id);
    }

    public function getCost() : int
    {
        return $this->getProduct()->getPrice($this->product_data) * $this->quantity;
    }
}
