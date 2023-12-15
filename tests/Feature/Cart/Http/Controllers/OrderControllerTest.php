<?php

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Http\Controllers\OrderController;
use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use function Pest\Laravel\get;

beforeEach(function() {
    $this->cart = app(\Antidote\LaravelCart\Domain\Cart::class);
});

it('will replace the contents of the cart with an incomplete order', function() {

    CartPanelPlugin::set('models.order', Order::class);
    CartPanelPlugin::set('models.order_item', \Antidote\LaravelCart\Models\OrderItem::class);

    //ensure maintenance mode is deactivated
    app()->maintenanceMode()->deactivate();

    $old_order_product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'Product in old order'
    ]);

    $cart_product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'cart product'
    ]);

    $customer = Customer::factory()->create();

    $order = Order::factory()->withProduct($old_order_product)->forCustomer($customer)->create();

    actingAsCustomer($customer, 'customer');
    $this->cart->add($cart_product);

    expect($this->cart->items()->count())->toBe(1);
    expect($this->cart->items()->first()->getProduct()->name)->toBe('cart product');

    $this->withoutExceptionHandling();
    $response = get('/checkout/replace_cart/'.$order->id);

    $response->assertRedirect('/cart');

    expect($this->cart->items()->count())->toBe(1);
    expect($this->cart->items()->first()->getProduct()->name)->toBe('Product in old order');
})
->coversClass(OrderController::class);

it('will add the contents of the cart with an incomplete order', function() {

    $old_order_product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'Product in old order'
    ]);

    $cart_product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'cart product'
    ]);

    $customer = Customer::factory()->create();

    $order = Order::factory()->withProduct($old_order_product)->forCustomer($customer)->create();

    actingAsCustomer($customer, 'customer');
    $this->cart->add($cart_product);

    expect($this->cart->items()->count())->toBe(1);
    expect($this->cart->items()->first()->getProduct()->name)->toBe('cart product');

    $response = get('/checkout/add_to_cart/'.$order->id);

    $response->assertRedirect('/cart');

    expect($this->cart->items()->count())->toBe(2);
})
->coversClass(OrderController::class);
