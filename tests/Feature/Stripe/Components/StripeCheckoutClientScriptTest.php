<?php

namespace Antidote\LaravelCart\Tests\Feature\Stripe\Components;

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\TestCase;
use Antidote\LaravelCartStripe\Components\StripeCheckoutClientScriptComponent;
use Antidote\LaravelCartStripe\Models\StripeOrder;
use Antidote\LaravelCartStripe\Testing\MockStripeHttpClient;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

class StripeCheckoutClientScriptTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithViews;
    use InteractsWithSession;

    /**
     * @test
     */
    public function it_will_render_the_stripe_checkout_component()
    {
        new MockStripeHttpClient();

        Customer::factory()->create();

        Config::set('laravel-cart.classes.order', StripeOrder::class);
        Config::set('laravel-cart.urls.order_complete', '/order-complete-url');

        $product = TestProduct::factory()->asSimpleProduct([
            'price' => 1000
        ])->create();

        Cart::add($product);

        $this->be(Customer::first());

        $order = Cart::createOrder(Customer::first());
        $order->setData('payment_intent_id', 'some_id');

        $component = $this->actingAs(Customer::first(), 'customer')->component(StripeCheckoutClientScriptComponent::class);

        expect($component->stripe_api_key)->toBe(config('laravel-cart.stripe.api_key'));
        expect($component->client_secret)->toBe(Cart::getActiveOrder()->getData('client_secret'));
        expect($component->checkout_confirm_url)->toBe(config('laravel-cart.urls.checkout_confirm'));
        expect($component->order_complete_url)->toBe("/order-complete-url?order_id=".StripeOrder::first()->id);
    }
}
