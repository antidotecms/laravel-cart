<?php

namespace Antidote\LaravelCart\Concerns;

trait ConfiguresOrderItem
{
    public function getTable()
    {
        return 'order_items';
    }

    public function initializeConfiguresOrderItem() : void
    {
        $this->fillable[] = 'name';

        $this->fillable[] = 'product_id';

        $this->fillable[] = 'product_data';
        $this->fillable[] = 'price';
        $this->fillable[] = 'quantity';

        $this->fillable[] = 'order_id';
    }

    public function getCasts() : array
    {
        return array_merge(parent::getCasts(), [
           'product_data' => 'array'
        ]);
    }
}
