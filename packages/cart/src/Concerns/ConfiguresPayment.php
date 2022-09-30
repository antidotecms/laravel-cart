<?php

namespace Antidote\LaravelCart\Concerns;

trait ConfiguresPayment
{
    public function initializeConfiguresPayment() : void
    {
        $this->fillable[] = getKeyFor('order');
    }
}
