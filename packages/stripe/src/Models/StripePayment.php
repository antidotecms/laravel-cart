<?php

namespace Antidote\LaravelCartStripe\Models;

use Antidote\LaravelCart\Contracts\Payment;
use Antidote\LaravelCartStripe\Concerns\ConfiguresStripePayment;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;

class StripePayment extends Payment
{
    use ConfiguresStripePayment;

    public function initialize(): void
    {
        PaymentIntent::create($this->order);
    }
}
