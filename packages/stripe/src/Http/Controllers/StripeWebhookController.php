<?php

namespace Antidote\LaravelCartStripe\Http\Controllers;

use Antidote\LaravelCart\Events\OrderCompleted;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Stripe\Event;
use Stripe\Exception\UnexpectedValueException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __construct()
    {
        //$this->middleware(\Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses::class);
    }

    public function __invoke(Request $request)
    {
        Stripe::setApiKey(CartPanelPlugin::get('stripe.secret_key'));
        $signature_header = $request->header('Stripe-Signature');
        $stripe_payment_intent_webhook_secret = CartPanelPlugin::get('stripe.webhook_secret');
        $payload = $request->getContent();

        try {
            $event =  Webhook::constructEvent($payload, $signature_header, $stripe_payment_intent_webhook_secret);
        }
        catch(\Stripe\Exception\SignatureVerificationException $e)
        {
            Log::info($e->getMessage());
            return response('', 400);
        }
        catch(UnexpectedValueException $e)
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

        $this->logStripeEvent($event);

        $this->handleEvent($event);
    }

    private function handleEvent(Event $event) : void
    {
        $order = getClassNameFor('order')::where('id', $event->data->object->metadata->order_id)->first();

        match($event->type) {
            'payment_intent.created' => call_user_func(function() use ($event, $order) {
                $this->createOrderLogItem('Stripe Payment Intent Created', $event, $order);
                $order->payment->data()->updateOrCreate([
                    'key' => 'payment_intent_id',
                    'value' => $event->data->object->id
                ]);
                $this->updateOrderStatus($event, $order);
            }),
            'payment_intent.succeeded' => call_user_func(function() use ($event, $order) {
                $this->createOrderLogItem('Stripe Payment Intent Succeeded', $event, $order);
                $this->updateOrderStatus($event, $order);
            }),
            'payment_intent.canceled' => call_user_func(function() use ($event, $order) {
                $this->createOrderLogItem('Stripe Payment Intent Cancelled', $event, $order);
                $this->updateOrderStatus($event, $order);
            }),
            'payment_intent.payment_failed' => call_user_func(function() use ($event, $order) {
                $this->createOrderLogItem('Stripe Payment Intent Failed', $event, $order);
                $this->updateOrderStatus($event, $order);
            }),
            'charge.succeeded' => call_user_func(function() use ($event, $order) {
                $this->createOrderLogItem('Stripe Charge Succeeded', $event, $order);
                $this->updateOrderStatus($event, $order);
                event(new OrderCompleted($order));
            }),
            default => $this->createOrderLogItem('Unknown Event', $event, $order)
        };
    }

    private function createOrderLogItem($event_name, $event, $order)
    {
//        $order_log_item = $order->log($event_name);
//        $order_log_item->event  = $event;
//        $order_log_item->save();
        $order->log($event_name);
        //@todo log event or leave in Stripe?
        //$order->log($event);
    }

    private function updateOrderStatus($event, $order)
    {
        $order->status = $event->data->object->status;
        $order->save();
    }

    private function logStripeEvent(Event $event)
    {
//        if(config('laravel-cart.stripe.log') === true) {
        if(CartPanelPlugin::get('stripe.logging') === true) {
            Log::info($event->toJSON());
        } else if(is_string(CartPanelPlugin::get('stripe.logging'))) {
            Log::channel(CartPanelPlugin::get('stripe.logging'))->info($event->toJSON());
        }
    }
}
