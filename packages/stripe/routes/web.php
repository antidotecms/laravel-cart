<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth:customer'])->group(function() {

    //@todo allow overriding with custom invokable controller to allow custom logic to determine if a sale can go through
    Route::get(\Antidote\LaravelCartFilament\CartPanelPlugin::get('urls.checkoutConfirm'), function () {

        $check = true;

        if($check) {
            $cart = app(\Antidote\LaravelCart\Domain\Cart::class);
            $cart->clear();
        }

        return response()->json(['check' => $check]);
    });

});

