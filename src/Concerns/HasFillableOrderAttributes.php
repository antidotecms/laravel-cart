<?php

namespace Antidote\LaravelCart\Concerns;

trait HasFillableOrderAttributes
{
    public function initializeHasFillableOrderAttributes() : void
    {
        $customer_class = config('laravel-cart.customer_class');
        $this->fillable[] = (new $customer_class)->getForeignKey();
    }
}
