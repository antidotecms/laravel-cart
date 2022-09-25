<?php

namespace Antidote\LaravelCart\Concerns;

trait ConfiguresPaymentMethod
{
    public function initializeConfiguresPaymentMethod() : void
    {
        $this->fillable[] = getKeyFor('order');
    }
}
