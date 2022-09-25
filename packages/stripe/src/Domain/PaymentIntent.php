<?php

namespace Antidote\LaravelCartStripe\Domain;

use Antidote\LaravelCart\Contracts\Order;
use Antidote\LaravelCartStripe\Concerns\HasStripeClient;
use Antidote\LaravelCartStripe\Models\StripePaymentMethod;
use Illuminate\Support\Str;
use InvalidArgumentException;

abstract class PaymentIntent
{
    use HasStripeClient;

    public static function create(Order $order) : StripePaymentMethod
    {
        $order_total = $order->getTotal();

        if($order_total >= 30 && $order_total <= 99999999) {
            $payment_intent_response = static::getClient()->paymentIntents->create([
                'amount' => $order->getTotal(),
                'currency' => 'gbp',
                'payment_method_types' => ['card'],
                'description' => 'Order #'.$order->id,
                'meta_data' => [
                    'order_id' => $order->id
                ],
                'receipt_email' => $order->customer->email
            ]);

            //$order_class = config('laravel-cart.order_class');
            $order_key = Str::snake(class_basename(config('laravel-cart.order_class'))).'_id';

            return StripePaymentMethod::create([
                'data' => $payment_intent_response,
                $order_key => $order->id
            ]);
        }

        throw new InvalidArgumentException('The order total must be greater than £0.30 and less that £999,999.99. See https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts');
    }

    public static function retrieve(string $payment_intent_id)
    {
        return static::getClient()->paymentIntents->retrieve($payment_intent_id);
    }
}
