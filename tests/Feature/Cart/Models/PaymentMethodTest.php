<?php

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestPayment;

it('has an order', function() {

    $customer = \Antidote\LaravelCart\Models\Customer::create([
        'name' => 'Tim Smith',
        'email' => 'info@titan21.co.uk',
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);

    $order = \Antidote\LaravelCart\Models\Order::create([
        'customer_id' => $customer->id
    ]);

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    Cart::add($product);

    $payment = TestPayment::create([
        'order_id' => $order->id
    ]);

    dump($payment->attributesToArray());

    $order->payment()->associate($payment);
    //$payment->order()->attach($order);
    $order->save();

    //dump($payment_method->attributesToArray());

    //dump($payment_method->order()->toSql());

    expect($order->payment->id)->toBe($payment->id);
    expect($payment->order->id)->toBe($order->id);
});
