<?php

//order factory test
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrder;

it('will correctly create an order with a simple product', function () {

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();

    $order = TestOrder::factory()
        ->forCustomer($customer)
        ->withProduct($product, 2)
        ->create();

    expect($order->getSubtotal())->toBe($product->getPrice() * 2);
});

it('will correctly create an order with a complex product', function () {

    $product = TestProduct::factory()->asComplexProduct()->create();
    $customer = TestCustomer::factory()->create();

    $order = TestOrder::factory()
        ->forCustomer($customer)
        ->withProduct($product, 2, ['width' => 10, 'height' => 10])
        ->create();

    expect($order->getSubtotal())->toBe($product->getPrice() * 2)->toBe(200);
});

it('will correctly create an order with a variable product', function() {

    $product = TestProduct::factory()->asVariableProduct()->create();
    $customer = TestCustomer::factory()->create();

    $order = TestOrder::factory()
        ->forCustomer($customer)
        ->withProduct($product, 2, ['width' => 10, 'height' => 10])
        ->create();

    expect($order->getSubtotal())->toBe($product->getPrice(['width' => 10, 'height' => 10]) * 2)->toBe(200);
});
