<?php

namespace Antidote\LaravelCartStripe\Service;

use Stripe\Webhook;
use Stripe\WebhookSignature;

class StripeWebhookService
{
    public function constructEvent(string $payload, ?string $signature, string $secret): \Stripe\Event
    {
        return Webhook::constructEvent($payload, $signature, $secret);
    }

    public function verifyHeader(string $payload, string $signature, string $secret): bool
    {
        return WebhookSignature::verifyHeader($payload,$signature, $secret);
    }
}
