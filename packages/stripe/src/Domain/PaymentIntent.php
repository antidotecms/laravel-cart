<?php

namespace Antidote\LaravelCartStripe\Domain;

use Antidote\LaravelCart\Contracts\Order;
use Antidote\LaravelCartStripe\Concerns\HasStripeClient;
use Antidote\LaravelCartStripe\Testing\MockStripeHttpClient;
use InvalidArgumentException;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;

abstract class PaymentIntent
{
    use HasStripeClient;

    public static function fake()
    {
        new MockStripeHttpClient();
    }

    public static function create(Order $order) : void
    {
        $order_total = $order->getTotal();

        if($order_total >= 30 && $order_total <= 99999999) {

            try {
                $payment_intent_response = static::getClient()->paymentIntents->create([
                    'amount' => $order->getTotal(),
                    'currency' => 'gbp',
                    'payment_method_types' => ['card'],
                    'description' => 'Order #'.$order->id,
                    'metadata' => [
                        'order_id' => $order->id
                    ],
                    'receipt_email' => $order->customer->email
                ]);

                $order->payment->client_secret = json_decode($payment_intent_response->getLastResponse()->body)->client_secret;
                $order->payment->save();
                $event = json_decode($payment_intent_response->getLastResponse()->body);
                self::logMessage($order, 'Payment Intent Created: '.$payment_intent_response->id, $event);

            } catch (CardException $e) {
                //problem with card
                //self::logMessage($order, 'Card Issue: '.$e->getMessage());
                self::logError($order, 'Card Error', $e, $event);
                //abort(500);

            } catch (InvalidRequestException $e) {

                //request was invalid
                //self::logMessage($order, 'Invalid API Request: '.$e->getMessage());
                self::logError($order, 'Invalid API Request', $e, $event);
                //abort(500);

            } catch (AuthenticationException $e) {

                //unable to auth
                //self::logMessage($order, 'Unable to authenticate with Stripe API: '.$e->getMessage());
                self::logError($order, 'Stripe Authentication Error', $e, $event);
                //abort(500);

            } catch (ApiErrorException $e) {

                //any other Stripe error
                //self::logMessage($order, 'Stripe API Error: '.$e->getMessage());
                self::logError($order, 'Stripe API Error', $e, $event);
                //abort(500);

            } catch (\Exception $e) {

                //application error
                //self::logMessage($order, 'Application Error: '.$e->getMessage());
                \Log::error($e->getMessage(), $e->getTrace());
                self::logError($order, 'Application Error', $e, $event);
                //abort(500);

            }

            return;
        }

        throw new InvalidArgumentException('The order total must be greater than £0.30 and less that £999,999.99. See https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts');
    }

    private static function logError($order, $type, $exception, $event)
    {
        self::logMessage($order, $type.' : '.$exception->getMessage(), $event);
        throw new \Exception();
    }

    private static function logMessage($order, $message, $event = [])
    {
        $order->logItems()->create([
            'message' => $message,
            'event' => $event
        ]);
    }

    public static function retrieve(string $payment_intent_id)
    {
        return static::getClient()->paymentIntents->retrieve($payment_intent_id);
    }
}
