<?php

use Antidote\LaravelCart\Facades\Cart;
use Tests\Fixtures\app\Models\Products\TestCustomer;
use Tests\Fixtures\app\Models\Products\TestOrder;
use Tests\Fixtures\app\Models\Products\TestProduct;

it('will create an order', function() {

    $product = TestProduct::factory()->asSimpleProduct()->create();

    Cart::add($product);

    $customer = TestCustomer::factory()->create();

    Cart::createOrder($customer);

    $order = TestOrder::first();

    expect(TestOrder::count())->toBe(1)
        ->and($order->items()->count())->toBe(1)
        ->and($order->items()->first()->id)->toBe($product->id)
        ->and(Cart::items())->toBeEmpty()
        ->and($order->customer->id)->toBe($customer->id);
});

test('a customer has an order', function () {

    $product = TestProduct::factory()->asSimpleProduct()->create();

    Cart::add($product);

    $customer = TestCustomer::factory()->create();

    Cart::createOrder($customer);

    expect($customer->orders()->count())->toBe(1);

});

it('will detail an order item', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create([
        'name' => 'A Simple Product'
    ]);

    $complex_product = TestProduct::factory()->asComplexProduct()->create([
        'name' => 'A Complex Product'
    ]);

    Cart::add($simple_product ,2);

    $product_data = [
        'width' => 10,
        'height' => 10
    ];

    Cart::add($complex_product, 1, $product_data);

    $customer = TestCustomer::factory()->create();

    $order = Cart::createOrder($customer);

    //change price of simple product and ensure it hasn't changed in cart
    $simple_product->productType->price = 2000;
    $simple_product->productType->save();

    expect($order->items()->first()->getName())->toBe('A Simple Product')
        ->and($order->items()->first()->getPrice())->toBe(1999)
        ->and($order->items()->first()->getQuantity())->toBe(2)
        ->and($order->items()->first()->getCost())->toBe(3998)
        ->and($order->items()->skip(1)->first()->getName())->toBe('10 x 10 object')
        ->and($order->items()->skip(1)->first()->getPrice())->toBe(100)
        ->and($order->items()->skip(1)->first()->getQuantity())->toBe(1)
        ->and($order->items()->skip(1)->first()->getCost())->toBe(100);

});
