<?php

namespace Antidote\LaravelCart\Models\Concerns;

trait ConfiguresProduct
{
    public function getTable()
    {
        return 'products';
    }

    public function initializeConfiguresProduct() : void
    {
        $this->fillable[] = 'name';
        $this->fillable[] = 'product_type_type';
        $this->fillable[] = 'product_type_id';
    }
}
