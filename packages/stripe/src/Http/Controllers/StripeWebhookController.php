<?php

namespace Antidote\LaravelCartStripe\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        if(!app()->environment('testing')) {
            Stripe::setApiKey(config('laravel-cart.stripe.secret_key'));
            $signature_header = $request->header('Stripe-Signature');
            $stripe_payment_intent_webhook_secret = config('laravel-cart.stripe.webhook_secret');
        }
        $payload = $request->getContent();

        try {
            if(!app()->environment('testing')) {
                $event = Webhook::constructEvent($payload, $signature_header, $stripe_payment_intent_webhook_secret);
            } else {
                $event = json_decode($payload);
            }
        }
        catch(\Stripe\Exception\SignatureVerificationException $e)
        {
            Log::info($e->getMessage());
            return response('', 400);
        }
        catch(\Exception $e)
        {
            Log::info($e->getTraceAsString());
            Log::info($e->getMessage());
            return response('', 400);
        }

        $order = getClassNameFor('order')::where('id', $event->data->object->metadata->order_id)->first();

        switch($event->type) {

            case "payment_intent.created":
                $order_log_item = $order->log('Stripe Payment Intent Created');
                $order_log_item->event  = json_encode($event);
                $order_log_item->save();

                $order->status = 'Payment Intent Created';
                $order->save();
            break;

            case "payment_intent.succeeded":
                $order_log_item = $order->log('Stripe Payment Intent Succeeded');
                $order_log_item->event  = $event;
                $order_log_item->save();
            break;

            case "charge.succeeded":
                //order successful
                $order_log_item = $order->log('Stripe Charge Succeeded');
                $order_log_item->event  = $event;
                $order_log_item->save();

                //send emails
            break;
        }
    }
}
