<?php

use Antidote\LaravelCart\Tests\Fixtures\cart\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\TestOrder;
use Antidote\LaravelCart\Tests\Fixtures\stripe\PaymentIntentHttpClient;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;

beforeEach(function () {
    new PaymentIntentHttpClient();
    Config::set('laravel-cart.payment_method_class', \Antidote\LaravelCartStripe\Models\StripePaymentMethod::class);
});

it('will return a payment intent object', function() {

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create();

    $order = TestOrder::factory()
        ->withProduct($product, 1)
        ->create();

    $payment_intent = PaymentIntent::create($order);

    expect(get_class($payment_intent))->toBe(\Antidote\LaravelCartStripe\Models\StripePaymentMethod::class);
    expect(get_class($order->paymentMethod))->toBe(\Antidote\LaravelCartStripe\Models\StripePaymentMethod::class);
});

it('will throw an exception if the amount is too low', function () {

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 29
    ])->create();

    $order = TestOrder::factory()
        ->withProduct($product, 1)
        ->create();

    $payment_intent = PaymentIntent::create($order);
})
->throws(InvalidArgumentException::class, 'The order total must be greater than £0.30 and less that £999,999.99. See https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts');

it('will throw an exception if the amount is too high', function () {

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 999999999
    ])->create();

    $order = TestOrder::factory()
        ->withProduct($product, 1)
        ->create();

    $payment_intent = PaymentIntent::create($order);
})
->throws(InvalidArgumentException::class, 'The order total must be greater than £0.30 and less that £999,999.99. See https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts');

it('will initialise a payment intent request', function () {

    $order = TestOrder::factory()
        ->withProduct(TestProduct::factory()->asSimpleProduct(['price' => 3000])->create(), 1)
        ->create();

    PaymentIntent::create($order);

    expect(\Antidote\LaravelCartStripe\Models\StripePaymentMethod::count())->toBe(1);



});
