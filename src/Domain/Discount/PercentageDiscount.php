<?php

namespace Antidote\LaravelCart\Domain\Discount;

use Antidote\LaravelCart\Abstracts\Discount;
use Antidote\LaravelCart\Models\Cart;
use Antidote\LaravelCart\Models\CartAdjustment;

class PercentageDiscount extends Discount
{
    public function amount(): int
    {
        return $this->cart->getSubtotal() * ($this->adjustment->parameters['percentage']/100);
    }

    public function isValid(): bool
    {
        return true;
    }
}
