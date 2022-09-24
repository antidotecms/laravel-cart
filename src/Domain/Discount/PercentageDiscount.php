<?php

namespace Antidote\LaravelCart\Domain\Discount;

use Antidote\LaravelCart\Contracts\Discount;

/**
 * property CartAdjustment $adjustment
 */
class PercentageDiscount extends Discount
{
    public function amount(int $subtotal): int
    {
        return $subtotal * ($this->adjustment->parameters['percentage']/100);
    }

    public function isValid(): bool
    {
        return true;
    }
}
