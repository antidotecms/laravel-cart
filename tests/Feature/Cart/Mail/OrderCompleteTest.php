<?php

use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;

it('will display the order details', function() {

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 123
    ])
    ->create([
       'name' => ' A Test Product'
    ]);

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $order = \Antidote\LaravelCart\Models\Order::factory()
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    $mailable = new \Antidote\LaravelCart\Mail\OrderComplete($order);

    $mailable->assertSeeInHtml('Please override config with a custom mailable.');
})
->coversClass(\Antidote\LaravelCart\Mail\OrderComplete::class);
