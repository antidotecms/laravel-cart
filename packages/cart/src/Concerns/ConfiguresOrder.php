<?php

namespace Antidote\LaravelCart\Concerns;

trait ConfiguresOrder
{
    public function initializeConfiguresOrder() : void
    {
        $customer_class = getClassNameFor('customer');
        $this->fillable[] = (new $customer_class)->getForeignKey();
    }
}
