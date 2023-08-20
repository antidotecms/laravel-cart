<?php

it('will create a collection of cart items from an array', function() {

    $cart_items = [
        [
            'product_id' => 1,
            'quantity' => 1,
            'product_data' => []
        ],
        [
            'product_id' => 1,
            'quantity' => 2,
            'product_data' => []
        ]
    ];

    $cart = new \Antidote\LaravelCart\DataTransferObjects\Cart(cart_items: $cart_items);

    expect($cart->cart_items)->toBeInstanceOf(\Illuminate\Support\Collection::class);
    expect($cart->cart_items->count())->toBe(2);
})
->covers(\Antidote\LaravelCart\DataTransferObjects\Casters\CartItemsCollectionCaster::class);
