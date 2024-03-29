<?php

namespace Antidote\LaravelCart\Tests\Feature\Cart\Listeners;

use Antidote\LaravelCart\CartServiceProvider;
use Antidote\LaravelCart\Mail\OrderComplete;
use Antidote\LaravelCart\Models\Adjustment;
use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Models\OrderAdjustment;
use Antidote\LaravelCart\Models\OrderItem;
use Antidote\LaravelCart\Models\OrderLogItem;
use Antidote\LaravelCart\Providers\EventServiceProvider;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

/**
 * @covers \Antidote\LaravelCart\Listeners\SendOrderConfirmation
 */
class SendOrderConfirmationTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../../Fixtures/Cart/migrations');
    }

    protected function defineEnv($app)
    {
        $app->config->set('laravel-cart.classes.order', Order::class);
        $app->config->set('laravel-cart.classes.order_item', OrderItem::class);
        $app->config->set('laravel-cart.classes.customer', Customer::class);
        $app->config->set('laravel-cart.classes.product', TestProduct::class);
        $app->config->set('laravel-cart.classes.order_adjustment', OrderAdjustment::class);
        $app->config->set('laravel-cart.classes.adjustment', Adjustment::class);
        $app->config->set('laravel-cart.classes.order_log_item', OrderLogItem::class);
    }

    protected function getPackageProviders($app)
    {
        return [
            CartServiceProvider::class,
            EventServiceProvider::class
        ];
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function will_generate_a_mail_when_an_order_is_complete()
    {

//        Config::set('laravel-cart.classes.order', TestStripeOrder::class);
//        Config::set('laravel-cart.classes.order_log_item', \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrderLogItem::class);
//        Config::set('laravel-cart.stripe.log', false);
        Config::set('laravel-cart.emails.order_complete', 'someone@somewhere.com');

        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = Customer::factory()->create();
        $order = Order::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        Mail::fake();

        (new \Antidote\LaravelCart\Listeners\SendOrderConfirmation())->handle(
            new \Antidote\LaravelCart\Events\OrderCompleted($order)
        );

        Mail::assertSent(OrderComplete::class, function(\Illuminate\Contracts\Mail\Mailable $mailable) use ($order) {
            expect(get_class($mailable))->toBe(OrderComplete::class);
            expect($mailable->bcc[0]['address'])->toBe('someone@somewhere.com');
            expect($mailable->to[0]['address'])->toBe($order->customer->email);
            return true;
        });
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function can_use_a_custom_mailable_for_the_OrderComplete_event(){

//        Config::set('laravel-cart.classes.order', TestStripeOrder::class);
//        Config::set('laravel-cart.classes.order_log_item', \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrderLogItem::class);
//        Config::set('laravel-cart.stripe.log', false);
        Config::set('laravel-cart.classes.mails.order_complete', \Antidote\LaravelCart\Tests\Fixtures\App\Mails\TestOrderCompleteMail::class);
        Config::set('laravel-cart.emails.order_complete', 'someone@somewhere.com');

        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = Customer::factory()->create();
        $order = Order::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        Mail::fake();

        (new \Antidote\LaravelCart\Listeners\SendOrderConfirmation())->handle(
            new \Antidote\LaravelCart\Events\OrderCompleted($order)
        );

        Mail::assertSent(\Antidote\LaravelCart\Tests\Fixtures\App\Mails\TestOrderCompleteMail::class, function(\Illuminate\Contracts\Mail\Mailable $mailable) use ($order) {
            expect(get_class($mailable))->toBe(\Antidote\LaravelCart\Tests\Fixtures\App\Mails\TestOrderCompleteMail::class);
            expect($mailable->bcc[0]['address'])->toBe('someone@somewhere.com');
            expect($mailable->to[0]['address'])->toBe($order->customer->email);
            return true;
        });
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function will_not_bcc_someone_in_if_the_email_address_is_null(){

        Config::set('laravel-cart.classes.order', Order::class);
        Config::set('laravel-cart.classes.order_log_item', OrderLogItem::class);
        Config::set('laravel-cart.stripe.log', false);
        Config::set('laravel-cart.classes.mails.order_complete', \Antidote\LaravelCart\Tests\Fixtures\App\Mails\TestOrderCompleteMail::class);
        Config::set('laravel-cart.emails.order_complete', null);

        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = Customer::factory()->create();
        $order = Order::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        Mail::fake();

        (new \Antidote\LaravelCart\Listeners\SendOrderConfirmation())->handle(
            new \Antidote\LaravelCart\Events\OrderCompleted($order)
        );

        Mail::assertSent(\Antidote\LaravelCart\Tests\Fixtures\App\Mails\TestOrderCompleteMail::class, function(\Illuminate\Contracts\Mail\Mailable $mailable) use ($order) {
            expect(get_class($mailable))->toBe(\Antidote\LaravelCart\Tests\Fixtures\App\Mails\TestOrderCompleteMail::class);
            expect($mailable->bcc)->toBeEmpty();
            expect($mailable->to[0]['address'])->toBe($order->customer->email);
            return true;
        });
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function will_not_bcc_someone_in_if_the_email_address_is_malformed(){

        Config::set('laravel-cart.classes.order', Order::class);
        Config::set('laravel-cart.classes.order_log_item', OrderLogItem::class);
        Config::set('laravel-cart.stripe.log', false);
        Config::set('laravel-cart.classes.mails.order_complete', \Antidote\LaravelCart\Tests\Fixtures\App\Mails\TestOrderCompleteMail::class);
        Config::set('laravel-cart.emails.order_complete', 'not a valid email address');

        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = Customer::factory()->create();
        $order = Order::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        Mail::fake();

        (new \Antidote\LaravelCart\Listeners\SendOrderConfirmation())->handle(
            new \Antidote\LaravelCart\Events\OrderCompleted($order)
        );

        Mail::assertSent(\Antidote\LaravelCart\Tests\Fixtures\App\Mails\TestOrderCompleteMail::class, function(\Illuminate\Contracts\Mail\Mailable $mailable) use ($order) {
            expect(get_class($mailable))->toBe(\Antidote\LaravelCart\Tests\Fixtures\App\Mails\TestOrderCompleteMail::class);
            expect($mailable->bcc)->toBeEmpty();
            expect($mailable->to[0]['address'])->toBe($order->customer->email);
            return true;
        });
    }
}
