<?php

namespace Antidote\LaravelCartStripe\Models;

use Antidote\LaravelCartStripe\Database\factories\StripeOrderFactory;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;

class StripeOrder extends \Antidote\LaravelCart\Models\Order
{
    protected static function newFactory()
    {
        return StripeOrderFactory::new();
    }

    public function updateStatus()
    {
        PaymentIntent::retrieveStatus($this);
    }

    public function isCompleted()
    {
        return $this->status == 'succeeded';
    }
}
