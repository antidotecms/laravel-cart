<?php

namespace Antidote\LaravelCart\Enums;

use Antidote\LaravelCartStripe\PaymentManager\StripePaymentManager;

enum PaymentMethod: string
{
    case Stripe = StripePaymentManager::class;
}
