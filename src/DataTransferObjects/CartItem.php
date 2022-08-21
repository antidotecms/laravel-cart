<?php

namespace Antidote\LaravelCart\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;
use Tests\Fixtures\app\Models\Products\Product;

class CartItem extends \Spatie\DataTransferObject\DataTransferObject implements Arrayable
{
    public int $product_id;
    public array | null $product_data;
    public int $quantity;

    public function getProduct()
    {
        //@todo why does this return incorrect model - return $this->product_type::find($this->product_id)->first();
        return Product::where('id', $this->product_id)->first();
    }
}
