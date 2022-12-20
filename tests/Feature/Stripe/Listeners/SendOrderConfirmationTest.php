<?php

use Antidote\LaravelCart\Mail\OrderComplete;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrder;
use Illuminate\Support\Facades\Mail;

it('will generate a mail when an order is completed', function() {

    Config::set('laravel-cart.classes.order', TestStripeOrder::class);
    Config::set('laravel-cart.classes.order_log_item', \Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrderLogItem::class);
    Config::set('laravel-cart.stripe.log', false);
    Config::set('laravel-cart.emails.order_complete', 'someone@somewhere.com');

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();
    $order = TestStripeOrder::factory()
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
});

it('can use a custom mailable for the OrderComplete event', function () {

    Config::set('laravel-cart.classes.order', TestStripeOrder::class);
    Config::set('laravel-cart.classes.order_log_item', \Antidote\LaravelCart\Tests\laravel\app\Models\TestStripeOrderLogItem::class);
    Config::set('laravel-cart.stripe.log', false);
    Config::set('laravel-cart.classes.mails.order_complete', \Antidote\LaravelCart\Tests\laravel\app\Mails\TestOrderCompleteMail::class);
    Config::set('laravel-cart.emails.order_complete', 'someone@somewhere.com');

    $product = TestProduct::factory()->asSimpleProduct()->create();
    $customer = TestCustomer::factory()->create();
    $order = TestStripeOrder::factory()
        ->withProduct($product)
        ->forCustomer($customer)
        ->create();

    Mail::fake();

    (new \Antidote\LaravelCart\Listeners\SendOrderConfirmation())->handle(
        new \Antidote\LaravelCart\Events\OrderCompleted($order)
    );

    Mail::assertSent(\Antidote\LaravelCart\Tests\laravel\app\Mails\TestOrderCompleteMail::class, function(\Illuminate\Contracts\Mail\Mailable $mailable) use ($order) {
        expect(get_class($mailable))->toBe(\Antidote\LaravelCart\Tests\laravel\app\Mails\TestOrderCompleteMail::class);
        expect($mailable->bcc[0]['address'])->toBe('someone@somewhere.com');
        expect($mailable->to[0]['address'])->toBe($order->customer->email);
        return true;
    });
});
