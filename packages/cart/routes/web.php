<?php

use Antidote\LaravelCart\Facades\Cart;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth:customer'])->group(function() {

    Route::get(config('laravel-cart.urls.order_complete'), function () {

        Cart::setActiveOrder(null);

        if ($order_id = request()->get('order_id')) {

            $order = getClassNameFor('order')::where('id', $order_id)->first();

            if ($order && $order->customer->id == auth()->guard('customer')->user()->id) {
                return view(config('laravel-cart.views.order_complete'), [
                    'order' => $order,
                    'completed' => $order->status == 'Charge Succeeded'
                ]);
            }
        }

        abort(404);
    });
});
