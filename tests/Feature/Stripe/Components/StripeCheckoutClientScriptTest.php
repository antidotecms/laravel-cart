<?php

namespace Antidote\LaravelCart\Tests\Feature\Stripe\Components;

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\TestCase;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Antidote\LaravelCartStripe\Components\StripeCheckoutClientScriptComponent;
use Antidote\LaravelCartStripe\Testing\MockStripeHttpClient;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

/**
 * @covers \Antidote\LaravelCartStripe\Components\StripeCheckoutClientScriptComponent
 */
class StripeCheckoutClientScriptTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithViews;
    use InteractsWithSession;

    private \Antidote\LaravelCart\Domain\Cart $cart;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cart = app(\Antidote\LaravelCart\Domain\Cart::class);
    }

    /**
     * @test
     */
    public function it_will_render_the_stripe_checkout_component()
    {
        new MockStripeHttpClient();

        Customer::factory()->create();

        //CartPanelPlugin::set('models.order', StripeOrder::class);
        Config::set('laravel-cart.stripe.api_key', 'stripe_api_key');

        $product = TestProduct::factory()->asSimpleProduct([
            'price' => 1000
        ])->create();

        $this->cart->add($product);

        $this->be(Customer::first());

        $order = $this->cart->createOrder(Customer::first());
        $order->setData('payment_intent_id', 'some_id');

        $component = $this->actingAs(Customer::first(), 'customer')->component(StripeCheckoutClientScriptComponent::class);

        expect($component->stripe_api_key)->toBe(config('laravel-cart.stripe.api_key'));
        expect($component->client_secret)->toBe($this->cart->getActiveOrder()->getData('client_secret'));
        expect($component->checkout_confirm_url)->toBe(CartPanelPlugin::get('urls.checkoutConfirm'));
        expect($component->order_complete_url)->toBe("order-complete?order_id=".Order::first()->id);
    }
}
