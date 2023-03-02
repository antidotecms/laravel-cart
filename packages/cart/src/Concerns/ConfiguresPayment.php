<?php

namespace Antidote\LaravelCart\Concerns;

trait ConfiguresPayment
{
    public function initializeConfiguresPayment() : void
    {
        $this->fillable[] = getKeyFor('order');
    }

    public function getTable()
    {
        return 'payments';
    }

}
