<?php

use Antidote\LaravelCart\Http\Controllers\OrderCompleteController;
use Antidote\LaravelCart\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get(config('laravel-cart.urls.order_complete'), OrderCompleteController::class)
    ->middleware(['web', 'auth:customer']);

Route::get('/checkout/replace_cart/{order_id}', [OrderController::class, 'setOrderItemsAsCart'])
    ->middleware(['web', 'auth:customer']);

Route::get('/checkout/add_to_cart/{order_id}', [OrderController::class, 'addOrderItemsToCart'])
    ->middleware(['web', 'auth:customer']);
