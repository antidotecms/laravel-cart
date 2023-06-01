<?php

use Antidote\LaravelCartStripe\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::post(config('laravel-cart.urls.stripe.webhook_handler'), StripeWebhookController::class)
//    ->middleware('stripe_webhook');
    ->middleware([
        \Antidote\LaravelCartStripe\Http\Middleware\AllowStripeWebhooksDuringMaintenance::class,
        \Antidote\LaravelCartStripe\Http\Middleware\WhitelistStripeIPAddresses::class
    ]);

Route::middleware(['web', 'auth:customer'])->group(function() {

    Route::get(config('laravel-cart.urls.checkout_confirm'), function () {

        $check = true;

        if($check) {
            \Antidote\LaravelCart\Facades\Cart::clear();
        }

        return response()->json(['check' => $check]);
    });

//    Route::get(config('laravel-cart.urls.order_complete'), function () {
//
//        //todo move this elsewhere?
//        Cart::setActiveOrder(null);
//
//        if ($order_id = request()->get('order_id')) {
//
//            $order = getClassNameFor('order')::where('id', $order_id)->first();
//
//            if ($order && $order->customer->id == auth()->guard('customer')->user()->id) {
//                return view(config('laravel-cart.views.order_complete'), [
//                    'order' => $order,
//                    'completed' => $order->status == 'Charge Succeeded'
//                ]);
//            }
//        }
//
//        abort(404);
//    });
});

