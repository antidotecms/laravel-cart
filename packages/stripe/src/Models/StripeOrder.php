<?php

namespace Antidote\LaravelCartStripe\Models;

use Antidote\LaravelCartStripe\Database\factories\StripeOrderFactory;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;

class StripeOrder extends \Antidote\LaravelCart\Contracts\Order
{
    protected static function newFactory()
    {
        return StripeOrderFactory::new();
    }

    public function updateStatus()
    {
        PaymentIntent::retrievePaymentIntent($this);
    }

    public function isCompleted()
    {
        return $this->status == 'charge.succeeded';
    }
}
