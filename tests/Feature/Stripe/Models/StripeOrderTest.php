<?php

use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrder;

it('provides a factory', function() {

    expect(\Antidote\LaravelCartStripe\Models\StripeOrder::factory())->toBeInstanceOf(\Antidote\LaravelCartStripe\Database\factories\StripeOrderFactory::class);

})
->covers(\Antidote\LaravelCartStripe\Models\StripeOrder::class);

it('will update the status of an order', function () {

})
->skip('Need to refactor payment intent and pull from service container rather than use abstract class');

it('will state whether an order is completed', function () {

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    $order = TestStripeOrder::factory()
        ->withProduct($product, 1)
        ->forCustomer(Customer::factory()->create())
        ->create();
    $order->status = 'succeeded';
    $order->save();
    expect($order->isCompleted())->toBeTrue();
})
->covers(\Antidote\LaravelCartStripe\Models\StripeOrder::class);
