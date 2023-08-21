<?php

namespace Antidote\LaravelCart\Tests\Feature\Stripe\Domain;

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Models\Adjustment;
use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\OrderAdjustment;
use Antidote\LaravelCart\Models\OrderItem;
use Antidote\LaravelCart\ServiceProvider;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrder;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrderLogItem;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;
use Antidote\LaravelCartStripe\Models\StripeOrder;
use Antidote\LaravelCartStripe\Testing\MockStripeHttpClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

/**
 * @covers \Antidote\LaravelCartStripe\Domain\PaymentIntent
 */
class PaymentIntentTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected function defineDatabaseMigrations()
    {
        //loads in product types
        $this->loadMigrationsFrom(__DIR__.'/../../../Fixtures/Cart/migrations');
    }

    protected function getPackageAliases($app)
    {
        return [
            'cart' => \Antidote\LaravelCart\Domain\Cart::class
        ];
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
            \Antidote\LaravelCartStripe\ServiceProvider::class
        ];
    }

    protected function defineEnv($app)
    {
        $app->config->set('laravel-cart.classes.order', StripeOrder::class);
        $app->config->set('laravel-cart.classes.order_item', OrderItem::class);
        $app->config->set('laravel-cart.classes.customer', Customer::class);
        $app->config->set('laravel-cart.classes.product', TestProduct::class);
        $app->config->set('laravel-cart.classes.order_adjustment', OrderAdjustment::class);
        $app->config->set('laravel-cart.classes.adjustment', Adjustment::class);
        $app->config->set('laravel-cart.classes.order_log_item', TestStripeOrderLogItem::class);
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function it_will_retrieve_a_payment_intents_status()
    {
        Config::set('laravel-cart.stripe.secret_key', 'dummy_key');

        (new MockStripeHttpClient())->with('status', 'a_status');

        $product = TestProduct::factory()->asSimpleProduct([
            'price' => 1000
        ])->create();

        $order = TestStripeOrder::factory()
            ->withProduct($product, 1)
            ->forCustomer(Customer::factory()->create())
            ->create();

        $order->setData('payment_intent_id', 'a_payment_intent_id');

        PaymentIntent::retrieveStatus($order);

        expect($order->status)->toBe('a_status');
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function it_will_throw_an_exception_if_payment_intent_id_is_not_set_on_order_when_retrieving_a_payment_intents_status()
    {
        Config::set('laravel-cart.stripe.secret_key', 'dummy_key');

        (new MockStripeHttpClient())->with('status', 'a_status');

        $product = TestProduct::factory()->asSimpleProduct([
            'price' => 1000
        ])->create();

        $order = TestStripeOrder::factory()
            ->withProduct($product, 1)
            ->forCustomer(Customer::factory()->create())
            ->create();

        $order->setData('payment_intent_id', '');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No payment intent id set on order');

        PaymentIntent::retrieveStatus($order);

        //expect($order->status)->toBe('a_status');
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function will_throw_an_exception_if_the_amount_is_too_low()
    {
        new MockStripeHttpClient();

        $product = TestProduct::factory()->asSimpleProduct([
            'price' => 1
        ])->create();

        $order = TestStripeOrder::factory()
            ->withProduct($product, 1)
            ->forCustomer(Customer::factory()->create())
            ->create();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The order total must be greater than £0.30 and less that £999,999.99. See https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts');

        PaymentIntent::create($order);
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function it_will_throw_an_exception_if_the_amount_is_too_high()
    {
        new MockStripeHttpClient();

        $product = TestProduct::factory()->asSimpleProduct([
            'price' => 1000000000
        ])->create();

        $order = TestStripeOrder::factory()
            ->withProduct($product, 1)
            ->create();

        //->throws(InvalidArgumentException::class, 'The order total must be greater than £0.30 and less that £999,999.99. See https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The order total must be greater than £0.30 and less that £999,999.99. See https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts');

        $payment_intent = PaymentIntent::create($order);
    }

    public static function orderLogErrorDataProvider(): array
    {
        return [
            'Card Exception' => [
                'exception_class' => \Stripe\Exception\CardException::class,
                'expected_message' => \Stripe\Exception\CardException::class
            ],
            'Invalid Request Exception' => [
                'exception_class' => \Stripe\Exception\InvalidRequestException::class,
                'expected_message' => \Stripe\Exception\InvalidRequestException::class
            ],
            'Authentication Exception' => [
                'exception_class' => \Stripe\Exception\AuthenticationException::class,
                'expected_message' => \Stripe\Exception\AuthenticationException::class
            ],
            'API Exception' => [
                'exception_class' => \Stripe\Exception\OAuth\InvalidClientException::class,
                'expected_message' => \Stripe\Exception\OAuth\InvalidClientException::class
            ],
            'Other Exception' => [
                'exception_class' => \InvalidArgumentException::class,
                'expected_message' => \InvalidArgumentException::class
            ]
        ];
    }

    /**
     * @test
     * @define-env defineEnv
     * @dataProvider orderLogErrorDataProvider
     */
    public function it_will_log_an_order_log_item($exception_class, $expected_message)
    {
        Config::set('laravel-cart.stripe.secret_key', 'dummy_key');

        //(new MockStripeHttpClient())->throwException($exception_class);
        PaymentIntent::fake()->throwException($exception_class);

        $simple_product = TestProduct::factory()->asSimpleProduct([
            'price' => 1000
        ])->create();

        $customer = Customer::factory()->create();

        Cart::add($simple_product);

        Config::set('laravel-cart.tax_rate', 0.2);

        $order = Cart::createOrder($customer);
        expect($order->total)->toBe(1200); //with 20% tax

        //$this->expectException($exception_class);

        //Cart::initializePayment($order);
        try {
            PaymentIntent::create($order);
        } catch (\Exception $e) {
            expect($order->logItems()->count())->toBe(1)
                ->and($order->logItems()->first()->message)->toStartWith($expected_message);
        }


//        expect($order->logItems()->count())->toBe(1)
//            ->and($order->logItems()->first()->message)->toStartWith($expected_message);

    }

    /**
     * @test
     * @define-env defineEnv
     * @dataProvider orderLogErrorDataProvider
     */
    public function it_will_throw_the_correct_exception($exception_class, $expected_message)
    {
        Config::set('laravel-cart.stripe.secret_key', 'dummy_key');

        //(new MockStripeHttpClient())->throwException($exception_class);
        PaymentIntent::fake()->throwException($exception_class);

        $simple_product = TestProduct::factory()->asSimpleProduct([
            'price' => 1000
        ])->create();

        $customer = Customer::factory()->create();

        Cart::add($simple_product);

        Config::set('laravel-cart.tax_rate', 0.2);

        $order = Cart::createOrder($customer);
        expect($order->total)->toBe(1200); //with 20% tax

        $this->expectException($exception_class);

        PaymentIntent::create($order);
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function it_will_not_generate_a_new_payment_intent_if_one_already_exists()
    {

        new MockStripeHttpClient();

        Config::set('laravel-cart.stripe.secret_key', 'dummy_key');

        $customer = Customer::factory()->create();

        $order = TestStripeOrder::factory()
            ->withProduct(TestProduct::factory()->asSimpleProduct(['price' => 3000])->create(), 1)
            ->forCustomer($customer)
            ->create();

        expect(TestStripeOrder::count())->toBe(1);

        PaymentIntent::create($order);

        $payment_intent_id = TestStripeOrder::first()->getData('payment_intent_id');

        PaymentIntent::create($order);

        expect(TestStripeOrder::first()->getData('payment_intent_id'))->toBe($payment_intent_id);

    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function it_will_generate_a_new_payment_intent_if_the_old_one_has_been_cancelled()
    {
        (new MockStripeHttpClient());

        Config::set('laravel-cart.stripe.secret_key', 'dummy_key');

        $customer = Customer::factory()->create();

        $order = TestStripeOrder::factory()
            ->withProduct(TestProduct::factory()->asSimpleProduct(['price' => 3000])->create(), 1)
            ->forCustomer($customer)
            ->create();

        expect(TestStripeOrder::count())->toBe(1);

        //Cart::initializePayment($order);
        PaymentIntent::create($order);

        $payment_intent_id = TestStripeOrder::first()->getData('payment_intent_id');

        (new MockStripeHttpClient())->with('canceled_at', 'hello')->with('amount', $order->total);
        PaymentIntent::create($order);

        expect(TestStripeOrder::first()->getData('payment_intent_id'))->not()->toBe($payment_intent_id);

    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function it_will_update_a_payment_intent_if_the_order_amount_has_changed()
    {
        (new MockStripeHttpClient())
            ->with('amount', 2000);

        Config::set('laravel-cart.stripe.secret_key', 'dummy_key');

        $customer = Customer::factory()->create();

        $order = TestStripeOrder::factory()
            ->withProduct(TestProduct::factory()->asSimpleProduct(['price' => 3000])->create(), 1)
            ->forCustomer($customer)
            ->create();

        expect(TestStripeOrder::count())->toBe(1);

        PaymentIntent::create($order);

        $payment_intent_id = TestStripeOrder::first()->getData('payment_intent_id');;

        PaymentIntent::create($order);

        expect(TestStripeOrder::first()->getData('payment_intent_id'))->toBe($payment_intent_id);
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function it_will_get_the_client_secret()
    {
        Config::set('laravel-cart.stripe.secret_key', 'dummy_key');

        (new MockStripeHttpClient())->with('client_secret', 'the client secret');

        $product = TestProduct::factory()->asSimpleProduct([
            'price' => 1000
        ])->create();

        $order = TestStripeOrder::factory()
            ->withProduct($product, 1)
            ->forCustomer(Customer::factory()->create())
            ->create();

        $order->setData('payment_intent_id', 'a payment io');

        PaymentIntent::getClientSecret($order);

        expect($order->getData('client_secret'))->toBe('the client secret');
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function it_will_not_query_stripe_if_the_client_secret_already_exists_on_the_order()
    {
//        Config::set('laravel-cart.stripe.secret_key', 'dummy_key');
//
//        (new MockStripeHttpClient())->with('client_secret', 'the client secret');

        $product = TestProduct::factory()->asSimpleProduct([
            'price' => 1000
        ])->create();

        $order = TestStripeOrder::factory()
            ->withProduct($product, 1)
            ->forCustomer(Customer::factory()->create())
            ->create();

        $order->setData('client_secret', 'the client secret');

        PaymentIntent::getClientSecret($order);

        expect($order->getData('client_secret'))->toBe('the client secret');
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function it_will_get_the_payment_intent()
    {
        Config::set('laravel-cart.stripe.secret_key', 'dummy_key');

        (new MockStripeHttpClient());

        $product = TestProduct::factory()->asSimpleProduct([
            'price' => 1000
        ])->create();

        $order = TestStripeOrder::factory()
            ->withProduct($product, 1)
            ->forCustomer(Customer::factory()->create())
            ->create();

        PaymentIntent::create($order);
    }

}
