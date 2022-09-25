<?php

namespace Antidote\LaravelCart\Concerns;

use Illuminate\Support\Str;

trait ConfiguresPaymentMethod
{
    public function initializeConfiguresPaymentMethod() : void
    {
        $order_key = Str::snake(class_basename(config('laravel-cart.order_class'))).'_id';
        $this->fillable[] = $order_key;
    }
}
