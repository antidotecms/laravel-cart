<?php

namespace Antidote\LaravelCartStripe\Concerns;

trait ConfiguresStripePayment
{
    public function initializeConfiguresStripeOrderLogItem() : void
    {
        $this->fillable[] = 'client_secret';
    }
}
