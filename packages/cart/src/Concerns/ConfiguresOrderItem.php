<?php

namespace Antidote\LaravelCart\Concerns;

trait ConfiguresOrderItem
{
    public function initializeConfiguresOrderItem() : void
    {
        $this->fillable[] = 'name';

        $this->fillable[] = getKeyFor('product');

        $this->fillable[] = 'product_data';
        $this->fillable[] = 'price';
        $this->fillable[] = 'quantity';
    }

    public function getCasts() : array
    {
        return array_merge(parent::getCasts(), [
           'product_data' => 'array'
        ]);
    }
}
