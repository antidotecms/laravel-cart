<?php

namespace Antidote\LaravelCart\Concerns;

trait ConfiguresPayment
{
    public function initializeConfiguresPayment() : void
    {
        $this->fillable[] = 'order_id';
    }

    public function getTable()
    {
        return 'payments';
    }

}
