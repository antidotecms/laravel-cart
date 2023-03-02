<?php

namespace Antidote\LaravelCartStripe\Models;

use Antidote\LaravelCartStripe\Concerns\ConfiguresStripeOrder;
use Antidote\LaravelCartStripe\Database\factories\StripeOrderFactory;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;

class StripeOrder extends \Antidote\LaravelCart\Models\Order
{
    use ConfiguresStripeOrder;

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
        return $this->status == 'succeeded';
    }
}
