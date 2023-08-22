<?php

use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrder;

it('provides a factory', function() {

    expect(\Antidote\LaravelCartStripe\Models\StripeOrder::factory())->toBeInstanceOf(\Antidote\LaravelCartStripe\Database\factories\StripeOrderFactory::class);

})
->covers(\Antidote\LaravelCartStripe\Models\StripeOrder::class);

it('will update the status of an order', function () {

    $order = TestStripeOrder::factory()->create();

    $mock = $this->mock(\Antidote\LaravelCartStripe\Domain\PaymentIntent::class, function(\Mockery\MockInterface $mock) use ($order) {
        $mock->shouldReceive('retrieveStatus')
            ->with($order)
            ->andReturns();
    });

    app()->bind(\Antidote\LaravelCartStripe\Domain\PaymentIntent::class, fn() => $mock);

    $order->updateStatus();

    //needed as mocking expectations does not constitute as an assertion
    $this->assertTrue(true);

})
->covers(\Antidote\LaravelCartStripe\Models\StripeOrder::class);
//->skip('Need to refactor payment intent and pull from service container rather than use abstract class');

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
