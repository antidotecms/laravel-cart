<?php

namespace Antidote\LaravelCart\Abstracts;

use Antidote\LaravelCart\Models\Cart;
use Antidote\LaravelCart\Models\CartAdjustment;

abstract class Discount
{
    protected Cart $cart;
    protected CartAdjustment $adjustment;

    public function __construct(Cart $cart, CartAdjustment $adjustment)
    {
        $this->cart = $cart;
        $this->adjustment = $adjustment;
    }

    /**
     * Returns the amount of discount to apply
     * @return int
     */
    public abstract function amount() : int;

    public abstract function isValid() : bool;
}
