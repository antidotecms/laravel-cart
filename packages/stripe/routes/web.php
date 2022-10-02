<?php

Route::get('/checkout/confirm', function () {
    return response()->json(['check' => true]);
});
