<?php

namespace Antidote\LaravelCartStripe\Domain;

use Antidote\LaravelCart\Contracts\Order;
use Antidote\LaravelCartStripe\Concerns\HasStripeClient;
use InvalidArgumentException;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\AuthenticationException;
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;

abstract class PaymentIntent
{
    use HasStripeClient;

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
                    'meta_data' => [
                        'order_id' => $order->id
                    ],
                    'receipt_email' => $order->customer->email
                ]);

                $order->payment->body = $payment_intent_response->getLastResponse()->body;
                $order->payment->save();

            } catch (CardException $e) {
                //problem with card
                self::logMessage($order, 'Card Issue: '.$e->getMessage());

            } catch (InvalidRequestException $e) {

                //request was invalid
                self::logMessage($order, 'Invalid API Request: '.$e->getMessage());

            } catch (AuthenticationException $e) {

                //unable to auth
                self::logMessage($order, 'Unable to authenticate with Stripe API: '.$e->getMessage());

            } catch (ApiErrorException $e) {

                //any other Stripe error
                self::logMessage($order, 'Stripe API Error: '.$e->getMessage());

            } catch (\Exception $e) {

                //application error
                self::logMessage($order, 'Application Error: '.$e->getMessage());

            } finally {
                return;
            }
        }

        throw new InvalidArgumentException('The order total must be greater than £0.30 and less that £999,999.99. See https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts');
    }

    private static function logMessage($order, $message)
    {
        $order->logItems()->create([
            'message' => $message
        ]);
    }

    public static function retrieve(string $payment_intent_id)
    {
        return static::getClient()->paymentIntents->retrieve($payment_intent_id);
    }
}
