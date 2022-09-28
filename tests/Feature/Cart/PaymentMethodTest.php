<?php

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\TestOrder;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\TestPayment;

it('has an order', function() {

    $customer = TestCustomer::create([
        'name' => 'Tim Smith',
        'email' => 'info@titan21.co.uk',
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);

    $order = TestOrder::create([
        'test_customer_id' => $customer->id
    ]);

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    Cart::add($product);

    $payment_method = TestPayment::create([
        'test_order_id' => $order->id
    ]);

    $order->paymentMethod()->associate($payment_method);
    $order->save();

    //dump($payment_method->attributesToArray());

    //dump($payment_method->order()->toSql());

    expect($order->paymentMethod->id)->toBe($payment_method->id);
    expect($payment_method->order->id)->toBe($order->id);
});
