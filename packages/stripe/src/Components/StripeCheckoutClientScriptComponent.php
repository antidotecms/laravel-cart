<?php

namespace Antidote\LaravelCartStripe\Components;

use Illuminate\View\Component;

class StripeCheckoutClientScriptComponent extends Component
{
    public function render()
    {
        return view('laravel-cart-stripe::components.stripe-checkout-client-script');
        //return "hello";
    }
}
