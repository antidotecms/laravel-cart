<?php

namespace Antidote\LaravelCart\Concerns;

/** @mixin Illuminate\Database\Eloquent\Model */

trait ConfiguresOrder
{
    public function getTable()
    {
        return 'orders';
    }

    public function initializeConfiguresOrder() : void
    {
        $customer_class = app('filament')->getPlugin('laravel-cart')->getModel('customer');
        $this->fillable[] = (new $customer_class)->getForeignKey();
        $this->append('total');
    }
}
