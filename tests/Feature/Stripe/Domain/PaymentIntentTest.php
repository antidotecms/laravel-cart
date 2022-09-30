<?php

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\cart\Models\TestOrder;
use Antidote\LaravelCart\Tests\Fixtures\stripe\PaymentIntentHttpClient;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;
use Antidote\LaravelCartStripe\Models\StripePayment;

beforeEach(function () {
    new PaymentIntentHttpClient();
    Config::set('laravel-cart.classes.payment', StripePayment::class);
});

it('will return a payment intent object', function() {

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create();

    $order = TestOrder::factory()
        ->withProduct($product, 1)
        ->create();

    expect(get_class($order->payment))->toBe(StripePayment::class);
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

    $customer = TestCustomer::factory()->create();

    $order = TestOrder::factory()
        ->withProduct(TestProduct::factory()->asSimpleProduct(['price' => 3000])->create(), 1)
        ->forCustomer($customer)
        ->create();

    PaymentIntent::create($order);

    expect(StripePayment::count())->toBe(1);

});

it('will set up a payment method', function () {

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    $customer = TestCustomer::factory()->create();

    Cart::add($simple_product);


    $order = Cart::createOrder($customer);
    expect($order->getTotal())->toBe(1000);

    Cart::initializePayment($order);

    expect(TestOrder::count())->toBe(1);
    expect(get_class($order->payment))->toBe(StripePayment::class);
});

it('will log an order log item', function ($exception_class, $expected_message) {

    (new PaymentIntentHttpClient())->throwException($exception_class);

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    $customer = TestCustomer::factory()->create();

    Cart::add($simple_product);


    $order = Cart::createOrder($customer);
    expect($order->getTotal())->toBe(1000);

    Cart::initializePayment($order);

    expect($order->logItems()->count())->toBe(1);
    expect($order->logItems()->first()->message)->toBe($expected_message);


})
->with([
    'Card Exception' => [
        'exception_class' => \Stripe\Exception\CardException::class,
        'expected_message' => 'Card Issue'
    ],
    'Invalid Request Exception' => [
        'exception_class' => \Stripe\Exception\InvalidRequestException::class,
        'expected_message' => 'Invalid API Request'
    ],
    'Authentication Exception' => [
        'exception_class' => \Stripe\Exception\AuthenticationException::class,
        'expected_message' => 'Unable to authenticate with Stripe API'
    ],
    'API Exception' => [
        'exception_class' => \Stripe\Exception\OAuth\InvalidClientException::class,
        'expected_message' => 'Stripe API Error'
    ],
    'Other Exception' => [
        'exception_class' => InvalidArgumentException::class,
        'expected_message' => 'Application Error'
    ]
]);
