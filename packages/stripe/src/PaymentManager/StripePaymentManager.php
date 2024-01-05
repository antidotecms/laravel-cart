<?php

namespace Antidote\LaravelCartStripe\PaymentManager;

use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;

class StripePaymentManager
{
    public function updateStatus(Order $order)
    {
        $payment_intent = app(PaymentIntent::class);
        $payment_intent->retrieveStatus($order);
    }

    public function isCompleted(Order $order)
    {
        return $order->status == 'succeeded';
        //dd('how to effect?');
    }
}
