<?php

namespace Antidote\LaravelCartStripe\Domain;

use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCartStripe\Concerns\HasStripeClient;
use Antidote\LaravelCartStripe\Models\StripeOrder;
use Antidote\LaravelCartStripe\Testing\MockStripeHttpClient;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

abstract class PaymentIntent
{
    use HasStripeClient;

    public static function fake() : MockStripeHttpClient
    {
        return new MockStripeHttpClient();
    }

    public static function create(StripeOrder $order) : void
    {
        self::checkValidOrderAmount($order);

        try {
            $event = [];
            $event = static::getPaymentIntent($order);
        } catch (\Exception $e) {
            self::logError($order, get_class($e), $e, $event);
            throw $e;
        }

    }

    private static function checkValidOrderAmount(StripeOrder $order): void
    {
        if($order->total < 30 || $order->total > 99999999) {
            throw new InvalidArgumentException('The order total must be greater than £0.30 and less that £999,999.99. See https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts');
        }
    }

    private static function logError($order, $type, $exception, $event)
    {
        static::logMessage($order, $type.' : '.$exception, $event);
        //return $exception;
    }

    private static function logMessage($order, $message, $event = [])
    {
        $order->logItems()->create([
            'message' => $message,
            'event' => $event
        ]);
    }

    public static function createPaymentIntent($order)
    {
        $payment_intent_response = static::getClient()->paymentIntents->create([
            'amount' => $order->total,
            'currency' => 'gbp',
            'payment_method_types' => ['card'],
            'description' => 'Order #'.$order->id,
            'metadata' => [
                'order_id' => $order->id
            ],
            'receipt_email' => $order->customer->email
        ]);

        $order->setData('payment_intent_id', json_decode($payment_intent_response->getLastResponse()->body)->id);
        $order->save();

        $order->refresh();

        $order->setData('client_secret', json_decode($payment_intent_response->getLastResponse()->body)->client_secret);
        $order->save();
        $event = json_decode($payment_intent_response->getLastResponse()->body);
        self::logMessage($order, 'Payment Intent Created: '.$payment_intent_response->id, $event);
        return $event;
    }

    public static function updatePaymentIntent($order)
    {
        $payment_intent_response = static::getClient()->paymentIntents->update($order->getData('payment_intent_id'), [
            'amount' => $order->total
        ]);

        //not amended payment intent id as it will be the same
        //$order->payment_intent_id = json_decode($payment_intent_response->getLastResponse()->body)->id;
        //$order->save();
        $order->setData('client_secret', json_decode($payment_intent_response->getLastResponse()->body)->client_secret);
        $order->save();
        $event = json_decode($payment_intent_response->getLastResponse()->body);
        self::logMessage($order, 'Payment Intent Updated: '.$payment_intent_response->id, $event);
        return $event;
    }

    public static function retrieveStatus($order)
    {
        if($order->getData('payment_intent_id')) {
            $payment_intent_response = static::getClient()->paymentIntents->retrieve($order->getData('payment_intent_id'));

            $order->status = json_decode($payment_intent_response->getLastResponse()->body)->status;
            $order->save();
        }
    }

    public static function getClientSecret(Order $order): string
    {
        $client_secret = '';

        if(!$client_secret = $order->getData('client_secret')) {
            $payment_intent_response = static::getClient()->paymentIntents->retrieve($order->getData('payment_intent_id'));

            $order->setData('client_secret', json_decode($payment_intent_response->getLastResponse()->body)->client_secret);
            $order->save();
            $client_secret = $order->getData('client_secret');
        }

        return $client_secret;
    }

    public static function getPaymentIntent(Order $order)
    {
        if(!$order->getData('payment_intent_id') && $order->status != 'succeeded')
        {
            $event = static::createPaymentIntent($order);
        }
        else
        {
            //get existing payment intent and check it is valid
            Log::info('retrieving payment intent');
            $payment_intent_response = static::getClient()->paymentIntents->retrieve($order->getData('payment_intent_id'));

            $body = json_decode($payment_intent_response->getLastResponse()->body);

            if($body->canceled_at) {
                //payment intent cancelled generate a new one
                //dd('create a new payment intent');
                Log::info('payment intent cancelled, deleting old one - creating a new one');
                $event = static::createPaymentIntent($order);
            }

            if($body->amount != $order->total) {
                //amend the payment intent
                //dd('update the payment intent');
                $event = static::updatePaymentIntent($order);
            }


        }

        return $event;
    }
}
