<?php

//order factory test
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;

it('will correctly create an order with a simple product', function () {

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->forCustomer($customer)
        ->withProduct($product, 2)
        ->create();

    expect($order->getSubtotal())->toBe($product->getPrice() * 2);
})
->covers(\Antidote\LaravelCart\Database\Factories\OrderFactory::class);

it('will correctly create an order with a complex product', function () {

    $product = TestProduct::factory()->asComplexProduct()->create();
    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->forCustomer($customer)
        ->withProduct($product, 2, ['width' => 10, 'height' => 10])
        ->create();

    expect($order->getSubtotal())->toBe($product->getPrice() * 2)->toBe(200);
})
->covers(\Antidote\LaravelCart\Database\Factories\OrderFactory::class);

it('will correctly create an order with a variable product', function() {

    $product = TestProduct::factory()->asVariableProduct()->create();
    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->forCustomer($customer)
        ->withProduct($product, 2, ['width' => 10, 'height' => 10])
        ->create();

    expect($order->getSubtotal())->toBe($product->getPrice(['width' => 10, 'height' => 10]) * 2)->toBe(200);
})
->covers(\Antidote\LaravelCart\Database\Factories\OrderFactory::class);
