<?php

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrder;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestPayment;

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

    $payment = TestPayment::create([
        'test_order_id' => $order->id
    ]);

    $order->payment()->associate($payment);
    $order->save();

    //dump($payment_method->attributesToArray());

    //dump($payment_method->order()->toSql());

    expect($order->payment->id)->toBe($payment->id);
    expect($payment->order->id)->toBe($order->id);
});
