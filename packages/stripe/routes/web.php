<?php

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCartStripe\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::post(config('laravel-cart.urls.stripe.webhook_handler'), StripeWebhookController::class);

Route::middleware(['web', 'auth:customer'])->group(function() {

    Route::get(config('laravel-cart.urls.checkout_confirm'), function () {
        return response()->json(['check' => true]);
    });

    Route::get(config('laravel-cart.urls.order_complete'), function () {

        $order = Cart::getActiveOrder();
        if($order) {
            Cart::setActiveOrder(null);

            return view(config('laravel-cart.views.order_complete'), [
                'order' => $order
            ]);
        } else {
            abort(404);
        }
    });
});

