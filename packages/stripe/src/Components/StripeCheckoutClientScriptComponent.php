<?php

namespace Antidote\LaravelCartStripe\Components;

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\Component;

class StripeCheckoutClientScriptComponent extends Component
{
    public string $stripe_api_key;

    public string $client_secret;
    public string $checkout_confirm_url;
    public string $order_complete_url;

    public function __construct(PaymentIntent $payment_intent, \Antidote\LaravelCart\Domain\Cart $cart)
    {
        //$this->validateRequiredConfig();

        $this->client_secret = $payment_intent->getClientSecret($cart->getActiveOrder());

        $this->checkout_confirm_url = app('filament')->getPlugin('laravel-cart')->getCheckoutConfirmUrl();
        $this->order_complete_url = app('filament')->getPlugin('laravel-cart')->getOrderCompleteUrl() .'?order_id='.$cart->getActiveOrder()->id;
        $this->stripe_api_key = config('laravel-cart.stripe.api_key');
    }

    /**
     * @deprecated
     */
    private function validateRequiredConfig()
    {
        $data = [
            'stripe_api_key' => config('laravel-cart.stripe.api_key'),
            'checkout_confirm_url' => config('laravel-cart.urls.checkout_confirm'),
            'order_complete_url' => config('laravel-cart.urls.order_complete')

        ];

        $rules = [
            'stripe_api_key' => 'required|string',
            'checkout_confirm_url' => 'required|string',
            'order_complete_url' => 'required|string'
        ];

        $messages = [
            'stripe_api_key' => 'No Stripe API Key set in config',
            'checkout_confirm_url' => 'No Stripe checkout confirm URL set in config',
            'order_complete_url' => 'No Stripe order complete URL set in config'
        ];

        $validator = Validator::make($data, $rules, $messages);

        if($validator->fails()) {
            //dd($validator->errors()->first());
            throw new \Exception($validator->errors()->first());
        } else {
            $this->stripe_api_key = config('laravel-cart.stripe.api_key');
            $this->order_complete_url = config('laravel-cart.urls.order_complete');
            $this->checkout_confirm_url = config('laravel-cart.urls.checkout_confirm');
        }
    }

    public function render()
    {
        return view('laravel-cart-stripe::components.stripe-checkout-client-script');
        //return "hello";
    }

}
