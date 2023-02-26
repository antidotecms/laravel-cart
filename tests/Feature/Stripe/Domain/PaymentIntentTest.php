<?php

namespace Antidote\LaravelCart\Tests\Feature\Stripe\Domain;

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\ServiceProvider;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestAdjustment;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestCustomer;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderAdjustment;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderItem;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrder;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrderLogItem;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;
use Antidote\LaravelCartStripe\Models\StripePayment;
use Antidote\LaravelCartStripe\Testing\MockStripeHttpClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

class PaymentIntentTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected function defineDatabaseMigrations()
    {
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
        $app->config->set('laravel-cart.classes.order', TestStripeOrder::class);
        $app->config->set('laravel-cart.classes.order_item', TestOrderItem::class);
        $app->config->set('laravel-cart.classes.customer', TestCustomer::class);
        $app->config->set('laravel-cart.classes.payment', StripePayment::class);
        $app->config->set('laravel-cart.classes.product', TestProduct::class);
        $app->config->set('laravel-cart.classes.order_adjustment', TestOrderAdjustment::class);
        $app->config->set('laravel-cart.classes.adjustment', TestAdjustment::class);
        $app->config->set('laravel-cart.classes.order_log_item', TestStripeOrderLogItem::class);
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function it_will_return_a_payment_intent_object()
    {
        new MockStripeHttpClient();

        $product = TestProduct::factory()->asSimpleProduct([
            'price' => 1999
        ])->create();

        $order = \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrder::factory()
            ->withProduct($product, 1)
            ->create();

        expect(get_class($order->payment))->toBe(StripePayment::class);
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
            ->forCustomer(TestCustomer::factory()->create())
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
            'price' => 999999999
        ])->create();

        $order = TestStripeOrder::factory()
            ->withProduct($product, 1)
            ->create();

        //->throws(InvalidArgumentException::class, 'The order total must be greater than £0.30 and less that £999,999.99. See https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The order total must be greater than £0.30 and less that £999,999.99. See https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts');

        $payment_intent = PaymentIntent::create($order);
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function it_will_initialise_a_payment_intent_request()
    {

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

    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function it_will_set_up_a_payment()
    {

        new MockStripeHttpClient();

        PaymentIntent::fake();

        Config::set('laravel-cart.stripe.secret_key', 'dummy_key');

        $simple_product = TestProduct::factory()->asSimpleProduct([
            'price' => 1000
        ])->create();

        $customer = TestCustomer::factory()->create();

        Cart::add($simple_product);

        Config::set('laravel-cart.tax_rate', 0.2);

        $order = Cart::createOrder($customer);
        expect($order->total)->toBe(1200); //inc VAT of 20%

        Cart::initializePayment($order);

        expect(TestStripeOrder::count())->toBe(1);
        expect(get_class($order->payment))->toBe(StripePayment::class);

        $order = TestStripeOrder::first();
        expect($order->logItems()->count())->toBe(1);
        expect($order->logItems()->first()->message)->toStartWith('Payment Intent Created');
    }

    public static function orderLogErrorDataProvider(): array
    {
        return [
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
                'exception_class' => \InvalidArgumentException::class,
                'expected_message' => 'Application Error'
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

        (new MockStripeHttpClient())->throwException($exception_class);

        $simple_product = TestProduct::factory()->asSimpleProduct([
            'price' => 1000
        ])->create();

        $customer = TestCustomer::factory()->create();

        Cart::add($simple_product);

        Config::set('laravel-cart.tax_rate', 0.2);

        $order = Cart::createOrder($customer);
        expect($order->total)->toBe(1200); //with 20% tax

        $this->expectException(\Exception::class);

        Cart::initializePayment($order);

        expect($order->logItems()->count())->toBe(1)
            ->and($order->logItems()->first()->message)->toStartWith($expected_message);
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function it_will_not_generate_a_new_payment_intent_if_one_already_exists()
    {

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

        //Cart::initializePayment($order);
        PaymentIntent::create($order);

        expect(StripePayment::count())->toBe(1);
        expect(TestStripeOrder::first()->payment_intent_id)->toBe($payment_intent_id);

    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function it_will_generate_a_new_payment_intent_if_the_old_one_has_been_cancelled()
    {

        (new MockStripeHttpClient());

        Config::set('laravel-cart.stripe.secret_key', 'dummy_key');

        $customer = TestCustomer::factory()->create();

        $order = TestStripeOrder::factory()
            ->withProduct(TestProduct::factory()->asSimpleProduct(['price' => 3000])->create(), 1)
            ->forCustomer($customer)
            ->create();

        expect(TestStripeOrder::count())->toBe(1);

        //Cart::initializePayment($order);
        PaymentIntent::create($order);

        $payment_intent_id = TestStripeOrder::first()->payment_intent_id;

        expect(StripePayment::count())->toBe(1);

        //Cart::initializePayment($order);

        (new MockStripeHttpClient())->with('canceled_at', 'hello')->with('amount', $order->total);
        PaymentIntent::create($order);

        expect(StripePayment::count())->toBe(1);
        expect(TestStripeOrder::first()->payment_intent_id)->not()->toBe($payment_intent_id);

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

        $customer = TestCustomer::factory()->create();

        $order = TestStripeOrder::factory()
            ->withProduct(TestProduct::factory()->asSimpleProduct(['price' => 3000])->create(), 1)
            ->forCustomer($customer)
            ->create();

        expect(TestStripeOrder::count())->toBe(1);

        //Cart::initializePayment($order);
        PaymentIntent::create($order);

        $payment_intent_id = TestStripeOrder::first()->payment_intent_id;

        expect(StripePayment::count())->toBe(1);

        //Cart::initializePayment($order);
        PaymentIntent::create($order);

        expect(StripePayment::count())->toBe(1);
        expect(TestStripeOrder::first()->payment_intent_id)->toBe($payment_intent_id);

    }

}
