<?php

namespace Antidote\LaravelCart\DataTransferObjects;

use Antidote\LaravelCart\Contracts\Product;
use Antidote\LaravelCart\Contracts\VariableProduct;
use Illuminate\Contracts\Support\Arrayable;
use Spatie\DataTransferObject\Attributes\CastWith;

class CartItem extends \Spatie\DataTransferObject\DataTransferObject implements Arrayable
{
    public int $product_id;
    public string $product_type;
    public int $quantity;
    public array | null $specification;

    public function getProduct() : Product | VariableProduct
    {
        //@todo why does this return incorrect model - return $this->product_type::find($this->product_id)->first();
        return $this->product_type::where('id', $this->product_id)->first();
    }
}
