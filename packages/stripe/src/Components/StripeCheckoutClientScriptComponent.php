<?php

namespace Antidote\LaravelCartStripe\Components;

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;
use Illuminate\View\Component;

class StripeCheckoutClientScriptComponent extends Component
{
    public string $stripe_api_key;

    public string $client_secret;
    public string $checkout_confirm_url;
    public string $order_complete_url;

    public function __construct(PaymentIntent $payment_intent, \Antidote\LaravelCart\Domain\Cart $cart)
    {
        $payment_intent = app(PaymentIntent::class);
        $payment_intent->create($cart->getActiveOrder());

        $this->client_secret = $payment_intent->getClientSecret($cart->getActiveOrder());

        $this->checkout_confirm_url = CartPanelPlugin::get('urls.checkoutConfirm');
        $this->order_complete_url = CartPanelPlugin::get('urls.orderComplete') .'?order_id='.$cart->getActiveOrder()->id;

        $this->stripe_api_key = CartPanelPlugin::get('stripe.api_key');
    }

    public function render()
    {
        return view('laravel-cart-stripe::components.stripe-checkout-client-script');
        //return "hello";
    }

}
