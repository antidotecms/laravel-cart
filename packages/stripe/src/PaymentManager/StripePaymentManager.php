<?php

namespace Antidote\LaravelCartStripe\PaymentManager;

use Antidote\LaravelCart\Contracts\PaymentManager;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;

class StripePaymentManager extends PaymentManager
{
    public function getCheckoutComponent(): string
    {
        return 'laravel-cart-stripe::checkout';
    }

    public function updateStatus(Order $order): void
    {
        $payment_intent = app(PaymentIntent::class);
        $payment_intent->retrieveStatus($order);
    }

    public function isCompleted(Order $order): bool
    {
        return $order->status == 'succeeded';
    }
}
