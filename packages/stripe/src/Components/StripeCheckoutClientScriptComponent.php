<?php

namespace Antidote\LaravelCartStripe\Components;

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;
use Illuminate\View\Component;

class StripeCheckoutClientScriptComponent extends Component
{
    public $stripe_api_key;
    public $client_secret;
    public $checkout_confirm_url;
    public $order_complete_url;

    public function __construct()
    {
        $this->stripe_api_key = config('laravel-cart.stripe.api_key');
        $this->client_secret = Cart::getActiveOrder()->getData('client_secret');

        if(!($this->client_secret = Cart::getActiveOrder()->getData('client_secret'))) {
            $this->client_secret = PaymentIntent::getClientSecret(Cart::getActiveOrder());
        }

        $this->checkout_confirm_url = config('laravel-cart.urls.checkout_confirm');
        $this->order_complete_url = config('laravel-cart.urls.order_complete').'?order_id='.Cart::getActiveOrder()->id;
    }

    public function render()
    {
        return view('laravel-cart-stripe::components.stripe-checkout-client-script');
        //return "hello";
    }
}
