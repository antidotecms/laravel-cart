<?php

namespace Antidote\LaravelCartStripe\Concerns;

trait ConfiguresStripePayment
{
    public function initializeConfiguresStripePayment() : void
    {
        $this->fillable[] = 'client_secret';
    }
}
