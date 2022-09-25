<?php

namespace Antidote\LaravelCartStripe\Concerns;

use Stripe\StripeClient;

trait HasStripeClient
{
    private static function getClient() : StripeClient
    {
        return new StripeClient(
            'pk_test_51Lkq5iJ2WiujycKtf5aPivR1LwwRgRYngAk4CA4Vu3cMJ0jUR4rofhp9jUqXBXUOFhqZzRlHw6yQcp6G4rqNaRFR00SkrWvAWx'
        );
    }
}
