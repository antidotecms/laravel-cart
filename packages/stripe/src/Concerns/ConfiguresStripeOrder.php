<?php

namespace Antidote\LaravelCartStripe\Concerns;

trait ConfiguresStripeOrder
{
    public function initializeConfiguresStripeOrder() : void
    {
        $this->fillable[] = 'payment_intent_id';
    }
}
