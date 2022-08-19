<?php

namespace Antidote\LaravelCart\Contracts;

use Antidote\LaravelCart\Models\CartAdjustment;

abstract class Discount
{
    protected CartAdjustment $adjustment;

    public function __construct(CartAdjustment $adjustment)
    {
        $this->adjustment = $adjustment;
    }

    /**
     * Returns the amount of discount to apply
     * @return int
     */
    public abstract function amount() : int;

    public abstract function isValid() : bool;
}
