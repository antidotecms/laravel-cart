<?php

use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestOrder;

it('will display the order details', function() {

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 123
    ])
    ->create([
       'name' => ' A Test Product'
    ]);

    $customer = TestCustomer::factory()->create();

    $order = TestOrder::factory()
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    $mailable = new \Antidote\LaravelCart\Mail\OrderComplete($order);

    $mailable->assertSeeInHtml('Please override config with a custom mailable.');
});
