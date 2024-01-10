<?php

namespace Antidote\LaravelCart\Enums;

use Antidote\LaravelCart\Contracts\PaymentManager;
use Antidote\LaravelCartStripe\PaymentManager\StripePaymentManager;

enum PaymentMethod
{
    case Stripe;

    public function manager(): PaymentManager
    {
        return match($this) {
            PaymentMethod::Stripe =>  new StripePaymentManager()
        };
    }
}
