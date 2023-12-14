<?php

namespace Antidote\LaravelCart\Tests\Feature\Cart\Listeners;

use Antidote\LaravelCart\Events\OrderCompleted;
use Antidote\LaravelCart\Listeners\SendOrderConfirmation;
use Antidote\LaravelCart\Mail\OrderComplete;
use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Models\OrderLogItem;
use Antidote\LaravelCart\Tests\Fixtures\App\Mails\TestOrderCompleteMail;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\TestCase;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

/**
 * @covers \Antidote\LaravelCart\Listeners\SendOrderConfirmation
 */
class SendOrderConfirmationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @define-env defineEnv
     */
    public function will_generate_a_mail_when_an_order_is_complete()
    {
        Config::set('laravel-cart.emails.order_complete', 'someone@somewhere.com');

        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = Customer::factory()->create();
        $order = Order::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        Mail::fake();

        (new SendOrderConfirmation())->handle(
            new OrderCompleted($order)
        );

        Mail::assertSent(OrderComplete::class, function(Mailable $mailable) use ($order) {
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
    public function can_use_a_custom_mailable_for_the_OrderComplete_event()
    {
        Config::set('laravel-cart.stripe.log', false);
        Config::set('laravel-cart.classes.mails.order_complete', TestOrderCompleteMail::class);
        Config::set('laravel-cart.emails.order_complete', 'someone@somewhere.com');

        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = Customer::factory()->create();
        $order = Order::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        Mail::fake();

        (new SendOrderConfirmation())->handle(
            new OrderCompleted($order)
        );

        Mail::assertSent(TestOrderCompleteMail::class, function(Mailable $mailable) use ($order) {
            expect(get_class($mailable))->toBe(TestOrderCompleteMail::class);
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
        Config::set('laravel-cart.classes.mails.order_complete', TestOrderCompleteMail::class);
        Config::set('laravel-cart.emails.order_complete', null);

        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = Customer::factory()->create();
        $order = Order::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        Mail::fake();

        (new SendOrderConfirmation())->handle(
            new OrderCompleted($order)
        );

        Mail::assertSent(TestOrderCompleteMail::class, function(Mailable $mailable) use ($order) {
            expect(get_class($mailable))->toBe(TestOrderCompleteMail::class);
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
        Config::set('laravel-cart.classes.mails.order_complete', TestOrderCompleteMail::class);
        Config::set('laravel-cart.emails.order_complete', 'not a valid email address');

        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = Customer::factory()->create();
        $order = Order::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        Mail::fake();

        (new SendOrderConfirmation())->handle(
            new OrderCompleted($order)
        );

        Mail::assertSent(TestOrderCompleteMail::class, function(Mailable $mailable) use ($order) {
            expect(get_class($mailable))->toBe(TestOrderCompleteMail::class);
            expect($mailable->bcc)->toBeEmpty();
            expect($mailable->to[0]['address'])->toBe($order->customer->email);
            return true;
        });
    }
}
