<?php

it('will create a valid cart item', function() {

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct()->create();

    $cartitem = new \Antidote\LaravelCart\DataTransferObjects\CartItem([
        'product_id' => $product->id,
        'quantity' => 1,
        'product_data' => []
    ]);

    $valid_cart_item = \Antidote\LaravelCart\Types\ValidCartItem::create($cartitem);

    //expect($valid_cart_item)->toBeInstanceOf(\Antidote\LaravelCart\Types\ValidCartItem::class);
    expect($valid_cart_item)->toBeInstanceOf(\Antidote\LaravelCart\DataTransferObjects\CartItem::class);
});

it('will throw an exception if the product has no associated product type', function () {

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->create();

    $cartitem = new \Antidote\LaravelCart\DataTransferObjects\CartItem([
        'product_id' => $product->id,
        'quantity' => 1,
        'product_data' => []
    ]);

    $valid_cart_item = \Antidote\LaravelCart\Types\ValidCartItem::create($cartitem);
})
->throws(InvalidArgumentException::class, 'Product has no product data type associated');

it('will throw an exception if the product is not valid', function () {

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asInvalidSimpleProduct()->create();

    $cartitem = new \Antidote\LaravelCart\DataTransferObjects\CartItem([
        'product_id' => $product->id,
        'quantity' => 1,
        'product_data' => []
    ]);

    $valid_cart_item = \Antidote\LaravelCart\Types\ValidCartItem::create($cartitem);
})
->throws(InvalidArgumentException::class, 'The cart item is invalid');

it('will throw an excpetion if the quantity is not greater than zero', function () {

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct()->create();

    $cartitem = new \Antidote\LaravelCart\DataTransferObjects\CartItem([
        'product_id' => $product->id,
        'quantity' => 0,
        'product_data' => []
    ]);

    $valid_cart_item = \Antidote\LaravelCart\Types\ValidCartItem::create($cartitem);

})
->throws(InvalidArgumentException::class, 'Quantity must be greater than or equal to one');
