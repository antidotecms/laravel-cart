<?php

namespace Antidote\LaravelCart\Tests\Feature\Stripe\Http\Controllers;

use Antidote\LaravelCart\Events\OrderCompleted;
use Antidote\LaravelCart\Http\Controllers\OrderCompleteController;
use Antidote\LaravelCart\ServiceProvider;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestCustomer;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderAdjustment;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderItem;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrder;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestStripeOrderLogItem;
use Antidote\LaravelCartStripe\Http\Controllers\StripeWebhookController;
use Antidote\LaravelCartStripe\Models\StripePayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class StripeWebhookControllerTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../../../../Fixtures/Cart/migrations');
    }

    protected function defineEnv($app)
    {
        $app->config->set('laravel-cart.classes.order', TestStripeOrder::class);
        $app->config->set('laravel-cart.classes.order_item', TestOrderItem::class);
        $app->config->set('laravel-cart.classes.customer', TestCustomer::class);
        $app->config->set('laravel-cart.classes.payment', StripePayment::class);
        $app->config->set('laravel-cart.classes.product', TestProduct::class);
        $app->config->set('laravel-cart.classes.order_adjustment', TestOrderAdjustment::class);
        $app->config->set('laravel-cart.classes.order_log_item', TestStripeOrderLogItem::class);

        $app->config->set('laravel-cart.urls.stripe.webhook_handler', '/stripe-webhook-handler');

        $app->config->set('laravel-cart.stripe.log', true);

//        $app->config->set('auth.guards', [
//            'web' => [
//                'driver' => 'session',
//                'provider' => 'test_customers',
//            ]
//        ]);
//
//        $app->config->set('auth.providers', [
//            'test_customers' => [
//                'driver' => 'eloquent',
//                'model' => TestCustomer::class,
//            ]
//        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
            \Antidote\LaravelCartStripe\ServiceProvider::class,
            //LivewireServiceProvider::class
        ];
    }

    private function createStripeEvent(string $type, array $parameters = [])
    {
        $event = match($type) {
            'payment_intent.created' => include __DIR__.'/../../../../Fixtures/Stripe/events/payment_intent.created.php',
            'payment_intent.succeeded' => include __DIR__.'/../../../../Fixtures/Stripe/events/payment_intent.succeeded.php',
            'charge.succeeded' => include __DIR__.'/../../../../Fixtures/Stripe/events/charge.succeeded.php',
            'payment_intent.cancelled' => include __DIR__.'/../../../../Fixtures/Stripe/events/payment_intent.cancelled.php',
            'payment_intent.payment_failed' => include __DIR__.'/../../../../Fixtures/Stripe/events/payment_intent.payment_failed.php',
            'unknown_event' => include __DIR__.'/../../../../Fixtures/Stripe/events/unknown_event.php'
        };

        return Arr::mergeDeep($parameters, $event);
    }

    protected function defineRoutes($router)
    {
        $router->post(config('laravel-cart.urls.stripe.webhook_handler'), StripeWebhookController::class);
        $router->get('/order-complete', OrderCompleteController::class)->middleware('auth:customer');
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function will_record_that_a_payment_intent_has_been_created()
    {
        Config::set('laravel-cart.stripe.log', true);

        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = TestCustomer::factory()->create();
        $order = TestStripeOrder::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        expect($order->total)->toBe($product->getPrice() + (int)(round($product->getPrice() * config('laravel-cart.tax_rate'))));

        $event = $this->createStripeEvent('payment_intent.created', ['data' => ['object' => ['metadata' => ['order_id' => $order->id], 'id' => 'payment_intent_identifier']]]);

        $this->mock(\Stripe\Webhook::class, function(\Mockery\MockInterface $mock) use ($event) {
            $mock->shouldReceive('constructEvent')
                ->andReturn($event);
        });

        Log::shouldReceive('info')
            ->with(json_encode($event))
            ->once();

        $response = $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);
        $response->assertSuccessful();

        $order->refresh();

        expect($order->logItems()->count())->toBe(1);

        expect($order->status)->toBe('requires_payment_method');
        expect($order->payment_intent_id)->toBe('payment_intent_identifier');

    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function will_record_that_a_payment_intent_was_successful()
    {
        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = TestCustomer::factory()->create();
        $order = TestStripeOrder::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        expect($order->total)->toBe($product->getPrice() + (int)(round($product->getPrice() * config('laravel-cart.tax_rate'))));

        $event = $this->createStripeEvent('payment_intent.succeeded', ['data' => ['object' => ['metadata' => ['order_id' => $order->id]]]]);

        $this->mock(\Stripe\Webhook::class, function(\Mockery\MockInterface $mock) use ($event) {
            $mock->shouldReceive('constructEvent')
                ->andReturn($event);
        });

        $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);

        $order->refresh();

        expect($order->logItems()->count())->toBe(1);

        expect($order->status)->toBe('requires_payment_method');
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function will_record_that_a_charge_was_successful()
    {
        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = TestCustomer::factory()->create();
        $order = TestStripeOrder::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        expect($order->total)->toBe($product->getPrice() + (int)(round($product->getPrice() * config('laravel-cart.tax_rate'))));

        $event = $this->createStripeEvent('charge.succeeded', ['data' => ['object' => ['metadata' => ['order_id' => $order->id]]]]);

        $this->mock(\Stripe\Webhook::class, function(\Mockery\MockInterface $mock) use ($event) {
            $mock->shouldReceive('constructEvent')
                ->andReturn($event);
        });

        $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);

        $order->refresh();

        expect($order->logItems()->count())->toBe(2);
        expect($order->logItems->first()->message)->toBe('Stripe Charge Succeeded');
        expect($order->logItems->skip(1)->first()->message)->toBe('Order complete mail sent to '.$customer->email);

        expect($order->status)->toBe('succeeded');
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function will_record_that_a_payment_intent_was_cancelled()
    {
        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = TestCustomer::factory()->create();
        $order = TestStripeOrder::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        expect($order->total)->toBe($product->getPrice() + (int)(round($product->getPrice() * config('laravel-cart.tax_rate'))));

        $event = $this->createStripeEvent('payment_intent.cancelled', ['data' => ['object' => ['metadata' => ['order_id' => $order->id]]]]);

        $this->mock(\Stripe\Webhook::class, function(\Mockery\MockInterface $mock) use ($event) {
            $mock->shouldReceive('constructEvent')
                ->andReturn($event);
        });

        $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);

        $order->refresh();

        expect($order->logItems()->count())->toBe(1);

        expect($order->status)->toBe('canceled');
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function will_record_that_a_payment_intent_payment_failed()
    {
        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = TestCustomer::factory()->create();
        $order = TestStripeOrder::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        expect($order->total)->toBe($product->getPrice() + (int)(round($product->getPrice() * config('laravel-cart.tax_rate'))));

        $event = $this->createStripeEvent('payment_intent.payment_failed', ['data' => ['object' => ['metadata' => ['order_id' => $order->id]]]]);

        $this->mock(\Stripe\Webhook::class, function(\Mockery\MockInterface $mock) use ($event) {
            $mock->shouldReceive('constructEvent')
                ->andReturn($event);
        });

        $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);

        $order->refresh();

        expect($order->logItems()->count())->toBe(1);

        expect($order->status)->toBe('requires_payment_method');
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function will_record_an_unknown_event()
    {
        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = TestCustomer::factory()->create();
        $order = TestStripeOrder::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        expect($order->total)->toBe($product->getPrice() + (int)(round($product->getPrice() * config('laravel-cart.tax_rate'))));

        $event = $this->createStripeEvent('unknown_event', ['data' => ['object' => ['metadata' => ['order_id' => $order->id]]]]);

        $this->mock(\Stripe\Webhook::class, function(\Mockery\MockInterface $mock) use ($event) {
            $mock->shouldReceive('constructEvent')
                ->andReturn($event);
        });

        $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);

        $order->refresh();

        expect($order->logItems()->count())->toBe(1);
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function will_log_an_event_to_the_regular_log_file()
    {
        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = TestCustomer::factory()->create();
        $order = TestStripeOrder::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        $event = $this->createStripeEvent('payment_intent.created', ['data' => ['object' => ['metadata' => ['order_id' => 1]]]]);


        Log::shouldReceive('info')
            ->with(json_encode($event))
            ->once();

        $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function will_log_an_event_to_the_specified_channel()
    {
        Config::set('laravel-cart.stripe.log', 'some_channel');

        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = TestCustomer::factory()->create();
        $order = TestStripeOrder::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        $event = $this->createStripeEvent('payment_intent.created', ['data' => ['object' => ['metadata' => ['order_id' => 1]]]]);

        //@see https://stackoverflow.com/a/23807415/1424591
        Log::shouldReceive('channel')
            ->with('some_channel')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('info')
            ->with(json_encode($event))
            ->once();

        $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function will_not_log_items()
    {
        Config::set('laravel-cart.stripe.log', false);

        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = TestCustomer::factory()->create();
        $order = TestStripeOrder::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        $event = $this->createStripeEvent('payment_intent.created', ['data' => ['object' => ['metadata' => ['order_id' => 1]]]]);

        //@see https://docs.mockery.io/en/latest/reference/expectations.html
        Log::shouldReceive('info')
            ->never();

        Log::shouldReceive('channel')
            ->never();

        $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);
    }

    /**
     * @test
     * @define-env defineEnv
     */
    public function will_generate_an_OrderCompleted_event()
    {
        $product = TestProduct::factory()->asSimpleProduct()->create();
        $customer = TestCustomer::factory()->create();
        $order = TestStripeOrder::factory()
            ->withProduct($product)
            ->forCustomer($customer)
            ->create();

        expect($order->total)->toBe($product->getPrice() + (int)(round($product->getPrice() * config('laravel-cart.tax_rate'))));

        $event = $this->createStripeEvent('charge.succeeded', ['data' => ['object' => ['metadata' => ['order_id' => $order->id]]]]);

        $this->mock(\Stripe\Webhook::class, function(\Mockery\MockInterface $mock) use ($event) {
            $mock->shouldReceive('constructEvent')
                ->andReturn($event);
        });

        Event::fake();

        $this->postJson(config('laravel-cart.urls.stripe.webhook_handler'), $event);

        Event::assertDispatched(OrderCompleted::class);
    }
}
