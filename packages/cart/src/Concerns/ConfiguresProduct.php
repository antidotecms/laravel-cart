<?php

namespace Antidote\LaravelCart\Concerns;

trait ConfiguresProduct
{
    public function initializeConfiguresProduct() : void
    {
        $this->fillable[] = 'product_type_type';
        $this->fillable[] = 'product_type_id';
    }
}
