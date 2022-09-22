<?php

namespace Antidote\LaravelCart\Concerns;

use Illuminate\Support\Str;

trait ConfiguresOrderItem
{
    public function initializeConfiguresOrderItem() : void
    {
        $this->fillable[] = 'name';

        $product_key = Str::snake(class_basename(config('laravel-cart.product_class'))).'_id';
        $this->fillable[] = $product_key;

        $this->fillable[] = 'product_data';
        $this->fillable[] = 'price';
        $this->fillable[] = 'quantity';

    }
}
