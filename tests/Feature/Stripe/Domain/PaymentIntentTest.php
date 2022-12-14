<?php

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrder;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;
use Antidote\LaravelCartStripe\Models\StripePayment;
use Antidote\LaravelCartStripe\Testing\MockStripeHttpClient;

beforeEach(function () {
    Config::set('laravel-cart.classes.order', TestStripeOrder::class);
    Config::set('laravel-cart.classes.payment', StripePayment::class);
});

it('will return a payment intent object', function() {

    new MockStripeHttpClient();

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create();

    $order = TestStripeOrder::factory()
        ->withProduct($product, 1)
        ->create();

    expect(get_class($order->payment))->toBe(StripePayment::class);
});

it('will throw an exception if the amount is too low', function () {

    new MockStripeHttpClient();

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 1
    ])->create();

    $order = TestStripeOrder::factory()
        ->withProduct($product, 1)
        ->forCustomer(TestCustomer::factory()->create())
        ->create();

    $payment_intent = PaymentIntent::create($order);
})
->throws(InvalidArgumentException::class, 'The order total must be greater than £0.30 and less that £999,999.99. See https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts');

it('will throw an exception if the amount is too high', function () {

    new MockStripeHttpClient();

    $product = TestProduct::factory()->asSimpleProduct([
        'price' => 999999999
    ])->create();

    $order = TestStripeOrder::factory()
        ->withProduct($product, 1)
        ->create();

    $payment_intent = PaymentIntent::create($order);
})
->throws(InvalidArgumentException::class, 'The order total must be greater than £0.30 and less that £999,999.99. See https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts');

it('will initialise a payment intent request', function () {

    new MockStripeHttpClient();

    Config::set('laravel-cart.stripe.secret_key', 'dummy_key');

    $customer = TestCustomer::factory()->create();

    $order = TestStripeOrder::factory()
        ->withProduct(TestProduct::factory()->asSimpleProduct(['price' => 3000])->create(), 1)
        ->forCustomer($customer)
        ->create();

    Cart::initializePayment($order);
    PaymentIntent::create($order);

    expect(StripePayment::count())->toBe(1);

});

it('will set up a payment', function () {

    new MockStripeHttpClient();

    PaymentIntent::fake();

    Config::set('laravel-cart.stripe.secret_key', 'dummy_key');

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    $customer = TestCustomer::factory()->create();

    Cart::add($simple_product);


    $order = Cart::createOrder($customer);
    expect($order->total)->toBe(1200); //inc VAT of 20%

    Cart::initializePayment($order);

    expect(TestStripeOrder::count())->toBe(1);
    expect(get_class($order->payment))->toBe(StripePayment::class);

    $order = TestStripeOrder::first();
    expect($order->logItems()->count())->toBe(1);
    expect($order->logItems()->first()->message)->toStartWith('Payment Intent Created');
});

it('will log an order log item', function ($exception_class, $expected_message) {

    (new MockStripeHttpClient())->throwException($exception_class);

    $simple_product = TestProduct::factory()->asSimpleProduct([
        'price' => 1000
    ])->create();

    $customer = TestCustomer::factory()->create();

    Cart::add($simple_product);


    $order = Cart::createOrder($customer);
    expect($order->total)->toBe(1200); //with 20% tax

    Cart::initializePayment($order);

    expect($order->logItems()->count())->toBe(1)
        ->and($order->logItems()->first()->message)->toStartWith($expected_message);


})
->throws(\Exception::class)
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

it('will not generate a new payment intent if one already exists', function () {

    new MockStripeHttpClient();

    Config::set('laravel-cart.stripe.secret_key', 'dummy_key');

    $customer = TestCustomer::factory()->create();

    $order = TestStripeOrder::factory()
        ->withProduct(TestProduct::factory()->asSimpleProduct(['price' => 3000])->create(), 1)
        ->forCustomer($customer)
        ->create();

    expect(TestStripeOrder::count())->toBe(1);

    Cart::initializePayment($order);
    PaymentIntent::create($order);

    $payment_intent_id = TestStripeOrder::first()->payment_intent_id;

    expect(StripePayment::count())->toBe(1);

    Cart::initializePayment($order);
    PaymentIntent::create($order);

    expect(StripePayment::count())->toBe(1);
    expect(TestStripeOrder::first()->payment_intent_id)->toBe($payment_intent_id);

});

it('will generate a new payment intent if the old one has been cancelled', function () {

    new MockStripeHttpClient();

    Config::set('laravel-cart.stripe.secret_key', 'dummy_key');

    $customer = TestCustomer::factory()->create();

    $order = TestStripeOrder::factory()
        ->withProduct(TestProduct::factory()->asSimpleProduct(['price' => 3000])->create(), 1)
        ->forCustomer($customer)
        ->create();

    expect(TestStripeOrder::count())->toBe(1);

    Cart::initializePayment($order);
    PaymentIntent::create($order);

    $payment_intent_id = TestStripeOrder::first()->payment_intent_id;

    expect(StripePayment::count())->toBe(1);

    Cart::initializePayment($order);

    (new MockStripeHttpClient())->with('canceled_at', 'hello');
    PaymentIntent::create($order);

    expect(StripePayment::count())->toBe(1);
    expect(TestStripeOrder::first()->payment_intent_id)->not()->toBe($payment_intent_id);

});

it('will update a payment intent if the order amount has changed', function () {

    (new MockStripeHttpClient())
        ->with('amount', 2000);

    Config::set('laravel-cart.stripe.secret_key', 'dummy_key');

    $customer = TestCustomer::factory()->create();

    $order = TestStripeOrder::factory()
        ->withProduct(TestProduct::factory()->asSimpleProduct(['price' => 3000])->create(), 1)
        ->forCustomer($customer)
        ->create();

    expect(TestStripeOrder::count())->toBe(1);

    Cart::initializePayment($order);
    PaymentIntent::create($order);

    $payment_intent_id = TestStripeOrder::first()->payment_intent_id;

    expect(StripePayment::count())->toBe(1);

    Cart::initializePayment($order);
    PaymentIntent::create($order);

    expect(StripePayment::count())->toBe(1);
    expect(TestStripeOrder::first()->payment_intent_id)->toBe($payment_intent_id);

});
