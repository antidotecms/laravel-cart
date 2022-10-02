<?php

namespace Antidote\LaravelCartStripe\Concerns;

use Stripe\StripeClient;

trait HasStripeClient
{
    private static function getClient() : StripeClient
    {
        return new StripeClient(
            config('laravel-cart.stripe.secret_key')
        );
    }
}
