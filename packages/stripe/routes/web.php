<?php

use Antidote\LaravelCartStripe\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::post(config('laravel-cart.urls.stripe.webhook_handler'), StripeWebhookController::class);

Route::middleware(['web', 'auth:customer'])->group(function() {

    Route::get(config('laravel-cart.urls.checkout_confirm'), function () {

        $check = true;

        if($check) {
            $cart = app(\Antidote\LaravelCart\Domain\Cart::class);
            $cart->clear();
        }

        return response()->json(['check' => $check]);
    });

});

