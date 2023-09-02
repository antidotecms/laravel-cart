<?php

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use function Pest\Laravel\get;

beforeEach(function() {
    $this->cart = app(\Antidote\LaravelCart\Domain\Cart::class);
});

it('will replace the contents of the cart with an incomplete order', function() {

    Config::set('laravel-cart.classes.order', \Antidote\LaravelCart\Models\Order::class);
    Config::set('laravel-cart.classes.order_item', \Antidote\LaravelCart\Models\OrderItem::class);

    $old_order_product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'Product in old order'
    ]);

    $cart_product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'cart product'
    ]);

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $order = \Antidote\LaravelCart\Models\Order::factory()->withProduct($old_order_product)->forCustomer($customer)->create();

    actingAsCustomer($customer, 'customer');
    $this->cart->add($cart_product);

    expect($this->cart->items()->count())->toBe(1);
    expect($this->cart->items()->first()->getProduct()->name)->toBe('cart product');

    $response = get('/checkout/replace_cart/'.$order->id);

    //$response = $this->get('/checkout/replace_cart/'.$order->id);
    $response->assertRedirect('/cart');

    expect($this->cart->items()->count())->toBe(1);
    expect($this->cart->items()->first()->getProduct()->name)->toBe('Product in old order');
})
->coversClass(\Antidote\LaravelCart\Http\Controllers\OrderController::class);

it('will add the contents of the cart with an incomplete order', function() {

    Config::set('laravel-cart.classes.order', \Antidote\LaravelCart\Models\Order::class);
    Config::set('laravel-cart.classes.order_item', \Antidote\LaravelCart\Models\OrderItem::class);

    $old_order_product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'Product in old order'
    ]);

    $cart_product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'cart product'
    ]);

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $order = \Antidote\LaravelCart\Models\Order::factory()->withProduct($old_order_product)->forCustomer($customer)->create();

    actingAsCustomer($customer, 'customer');
    $this->cart->add($cart_product);

    expect($this->cart->items()->count())->toBe(1);
    expect($this->cart->items()->first()->getProduct()->name)->toBe('cart product');

    $response = get('/checkout/add_to_cart/'.$order->id);

    //$response = $this->get('/checkout/replace_cart/'.$order->id);
    $response->assertRedirect('/cart');

    expect($this->cart->items()->count())->toBe(2);
    //expect($this->cart->items()->first()->getProduct()->name)->toBe('Product in old order');
})
->coversClass(\Antidote\LaravelCart\Http\Controllers\OrderController::class);
