<?php

use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrder;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestOrderItem;
use function Pest\Laravel\get;

it('will replace the contents of the cart with an incomplete order', function() {

    Config::set('laravel-cart.classes.order', TestOrder::class);
    Config::set('laravel-cart.classes.order_item', TestOrderItem::class);

    $old_order_product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'Product in old order'
    ]);

    $cart_product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'cart product'
    ]);

    $customer = \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestCustomer::factory()->create();

    $order = TestOrder::factory()->withProduct($old_order_product)->forCustomer($customer)->create();

    actingAsCustomer($customer, 'customer');
    Cart::add($cart_product);

    expect(Cart::items()->count())->toBe(1);
    expect(Cart::items()->first()->getProduct()->name)->toBe('cart product');

    $response = get('/checkout/replace_cart/'.$order->id);

    //$response = $this->get('/checkout/replace_cart/'.$order->id);
    $response->assertRedirect('/cart');

    expect(Cart::items()->count())->toBe(1);
    expect(Cart::items()->first()->getProduct()->name)->toBe('Product in old order');
});

it('will add the contents of the cart with an incomplete order', function() {

    Config::set('laravel-cart.classes.order', TestOrder::class);
    Config::set('laravel-cart.classes.order_item', TestOrderItem::class);

    $old_order_product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'Product in old order'
    ]);

    $cart_product = TestProduct::factory()->asSimpleProduct()->create([
        'name' => 'cart product'
    ]);

    $customer = \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestCustomer::factory()->create();

    $order = TestOrder::factory()->withProduct($old_order_product)->forCustomer($customer)->create();

    actingAsCustomer($customer, 'customer');
    Cart::add($cart_product);

    expect(Cart::items()->count())->toBe(1);
    expect(Cart::items()->first()->getProduct()->name)->toBe('cart product');

    $response = get('/checkout/add_to_cart/'.$order->id);

    //$response = $this->get('/checkout/replace_cart/'.$order->id);
    $response->assertRedirect('/cart');

    expect(Cart::items()->count())->toBe(2);
    //expect(Cart::items()->first()->getProduct()->name)->toBe('Product in old order');
});
