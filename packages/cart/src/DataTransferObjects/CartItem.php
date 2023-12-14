<?php

namespace Antidote\LaravelCart\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;
use Spatie\DataTransferObject\DataTransferObject;

class CartItem extends DataTransferObject implements Arrayable
{
    public int $product_id;
    public ?array $product_data;
    public int $quantity;

    public function getProduct()
    {
        return getClassNameFor('product')::with('productType')->find($this->product_id);
    }

    public function getCost() : int
    {
        return $this->getProduct()->getPrice($this->product_data ?? []) * $this->quantity;
    }
}
