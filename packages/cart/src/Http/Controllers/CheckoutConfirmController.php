<?php

namespace Antidote\LaravelCart\Http\Controllers;

class CheckoutConfirmController
{
    public function __invoke()
    {
        $check = true;

        if($check) {
            $cart = app(\Antidote\LaravelCart\Domain\Cart::class);
            $cart->clear();
        }

        return response()->json(['check' => $check]);
    }
}
