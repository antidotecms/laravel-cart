<?php

namespace Antidote\LaravelCart\Concerns;

use Illuminate\Support\Str;

trait ConfiguresOrderAdjustment
{
    public function initializeConfiguresOrderAdjustment() : void
    {
        $this->fillable[] = 'name';
        $this->fillable[] = 'class';
        $this->fillable[] = 'parameters';

        $order_key = Str::snake(class_basename(config('laravel-cart.order_class'))).'_id';
        $this->fillable[] = $order_key;
    }

    public function getCasts() : array
    {
        return array_merge(parent::getCasts(), [
            'parameters' => 'array'
        ]);
    }
}
