<?php

use Antidote\LaravelCart\Http\Controllers\OrderCompleteController;
use Illuminate\Support\Facades\Route;

Route::get(config('laravel-cart.urls.order_complete'), OrderCompleteController::class)
    ->middleware(['web', 'auth:customer']);
