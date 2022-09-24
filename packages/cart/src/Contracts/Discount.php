<?php

namespace Antidote\LaravelCart\Contracts;

use Antidote\LaravelCart\Models\CartAdjustment;

abstract class Discount
{
    protected CartAdjustment|OrderAdjustment $adjustment;

    public function __construct(CartAdjustment|OrderAdjustment $adjustment)
    {
        $this->adjustment = $adjustment;
    }

    /**
     * Returns the amount of discount to apply
     * @return int
     */
    public abstract function amount(int $subtotal) : int;

    public abstract function isValid() : bool;
}
