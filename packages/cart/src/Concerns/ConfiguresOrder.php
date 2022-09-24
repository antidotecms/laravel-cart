<?php

namespace Antidote\LaravelCart\Concerns;

trait ConfiguresOrder
{
    public function initializeConfiguresOrder() : void
    {
        $customer_class = config('laravel-cart.customer_class');
        $this->fillable[] = (new $customer_class)->getForeignKey();
    }
}
