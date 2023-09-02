<?php

it('has a product and cost', function() {

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct(['price' => 2000])->create();

    $cart_item = new \Antidote\LaravelCart\DataTransferObjects\CartItem([
        'product_id' => $product->id,
        'quantity' => 2,
        'product_data' => []
    ]);

    expect($cart_item->getProduct()->id)->toBe($product->id);
    expect($cart_item->getCost())->toBe(4000);
})
->covers(\Antidote\LaravelCart\DataTransferObjects\CartItem::class);

it('has a product and cost via the cart facade', function () {

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct(['price' => 2000])->create();

    $this->be($customer);

    $cart = app(\Antidote\LaravelCart\Domain\Cart::class);
    $cart->add($product);

    expect($cart->getSubtotal())->toBeGreaterThan(0);
    expect($cart->getTotal())->toBeGreaterThan(0);

})
->covers(\Antidote\LaravelCart\DataTransferObjects\CartItem::class);
