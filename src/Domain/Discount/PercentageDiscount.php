<?php

namespace Antidote\LaravelCart\Domain\Discount;

use Antidote\LaravelCart\Contracts\Discount;
use Antidote\LaravelCart\Facades\Cart;

class PercentageDiscount extends Discount
{
    public function amount(): int
    {
        return Cart::getSubtotal() * ($this->adjustment->parameters['percentage']/100);
    }

    public function isValid(): bool
    {
        return true;
    }
}
