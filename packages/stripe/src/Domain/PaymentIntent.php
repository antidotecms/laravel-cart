<?php

namespace Antidote\LaravelCartStripe\Domain;

use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCartStripe\Models\StripeOrder;
use Antidote\LaravelCartStripe\Testing\MockStripeHttpClient;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Stripe\StripeClient;

class PaymentIntent
{
    private function getClient() : StripeClient
    {
        return new StripeClient(
            config('laravel-cart.stripe.secret_key')
        );
    }

    public function fake() : MockStripeHttpClient
    {
        return new MockStripeHttpClient();
    }

    public function create(StripeOrder $order) : void
    {
        $this->checkValidOrderAmount($order);

        try {
            $event = [];
            $event = $this->getPaymentIntent($order);
        } catch (\Exception $e) {
            $this->logError($order, get_class($e), $e, $event);
            throw $e;
        }

    }

    private function checkValidOrderAmount(StripeOrder $order): void
    {
        if($order->total < 30 | $order->total > 99999999) {
            throw new InvalidArgumentException('The order total must be greater than £0.30 and less that £999,999.99. See https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts');
        }
    }

    private function logError($order, $type, $exception, $event)
    {
        $this->logMessage($order, $type.' : '.$exception, $event);
        //return $exception;
    }

    private function logMessage($order, $message, $event = [])
    {
        $order->logItems()->create([
            'message' => $message,
            'event' => $event
        ]);
    }

    private function createPaymentIntent($order)
    {
        //throws ApiErrorException
        $payment_intent = $this->getClient()->paymentIntents->create([
            'amount' => $order->total,
            'currency' => 'gbp',
            'payment_method_types' => ['card'],
            'description' => 'Order #'.$order->id,
            'metadata' => [
                'order_id' => $order->id
            ],
            'receipt_email' => $order->customer->email
        ]);

        $order->setData('payment_intent_id', json_decode($payment_intent->getLastResponse()->body)->id);
        $order->setData('client_secret', json_decode($payment_intent->getLastResponse()->body)->client_secret);
        $order->save();

        $order->refresh();

        $event = json_decode($payment_intent->getLastResponse()->body);
        $this->logMessage($order, 'Payment Intent Created: '.$payment_intent->id, $event);
        return $event;
    }

    private function updatePaymentIntent($order)
    {
//        $payment_intent_response = $this->getClient()->paymentIntents->update($order->getData('payment_intent_id'), [
//            'amount' => $order->total
//        ]);

        $payment_intent_response = $this->queryPaymentIntent($order->getData('payment_intent_id'), [
            'amount' => $order->total
        ]);

        //not amended payment intent id as it will be the same
        //$order->payment_intent_id = json_decode($payment_intent_response->getLastResponse()->body)->id;
        //$order->save();
        $order->setData('client_secret', $payment_intent_response->client_secret);
        $order->save();
        //$event = json_decode($payment_intent_response->getLastResponse()->body);
        $this->logMessage($order, 'Payment Intent Updated: '.$payment_intent_response->id, $payment_intent_response);
        return $payment_intent_response;
    }

    public function retrieveStatus(StripeOrder $order)
    {
        if($order->getData('payment_intent_id')) {

            //@todo may throw Stripe\Exception\ApiErrorException
            $payment_intent_response = $this->queryPaymentIntent($order->getData('payment_intent_id'));

            $order->status = $payment_intent_response->status;
            $order->save();
        } else {
            //@todo possibly create a custome rexception here, hanlde it and request a new payment intent
            throw new \Exception('No payment intent id set on order');
        }
    }

    public function getClientSecret(Order $order): string
    {
        $client_secret = '';

        if(!$client_secret = $order->getData('client_secret')) {
            //@todo may throw Stripe\Exception\ApiErrorException
            $payment_intent_response = $this->queryPaymentIntent($order->getData('payment_intent_id'));

            $order->setData('client_secret', $payment_intent_response->client_secret);
            $order->save();
            $client_secret = $order->getData('client_secret');
        }

        return $client_secret;
    }

    private function getPaymentIntent(Order $order): \stdClass
    {
        $payment_intent_response = null;

        $payment_intent_response = $this->requiresPaymentIntent($order, $payment_intent_response, function() use ($order) {
            return $this->createPaymentIntent($order);
        });

        Log::info('retrieving payment intent');
        $payment_intent_response = $this->queryPaymentIntent($order->getData('payment_intent_id'));

        $payment_intent_response = $this->paymentIntentCancelled($payment_intent_response, function() use ($order) {
            Log::info('payment intent cancelled, deleting old one - creating a new one');
            return $this->createPaymentIntent($order);
        });

        $payment_intent_response = $this->mismatchedTotals($order,$payment_intent_response, function() use ($order) {
            return $this->updatePaymentIntent($order);
        });

        return $payment_intent_response;
    }

    private function requiresPaymentIntent($order, $event, \Closure $callback): \stdClass|null
    {
        if(!$order->isCompleted() & !$order->getData('payment_intent_id')) {
            $event = $callback();
        }

        return $event;
    }

    private function paymentIntentCancelled($event, \Closure $callback): \stdClass|null
    {
        if($event->canceled_at) {
            $event = $callback();
        }

        return $event;
    }

    private function mismatchedTotals($order, $event, \Closure $callback): \stdClass|null
    {
        if($event->amount != $order->total) {
            $event = $callback();
        }

        return $event;
    }

    private function queryPaymentIntent($payment_intent_id, $params = []): \stdClass
    {
        $response = $this->getClient()->paymentIntents->retrieve($payment_intent_id, $params);
        return (object) $response->getLastResponse()->json;
    }
}
