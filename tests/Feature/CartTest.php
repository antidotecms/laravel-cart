<?php

namespace Tests\Feature;

use Antidote\LaravelCart\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Fixtures\app\Models\ComplexProduct;
use Tests\Fixtures\app\Models\Customer;
use Tests\Fixtures\app\Models\SimpleProduct;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_customer_can_add_a_product()
    {
        $customer = Customer::create([
            'name' => 'Customer Smith',
            'email' => 'customer@titan21.co.uk',
            'password' => Hash::make('password')
        ]);

        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        $this->assertEquals(1, Customer::count());
        $this->assertEquals(1, SimpleProduct::count());

        $customer->cart->add($product);

        $this->assertEquals(1, $customer->cart->cartitems->count());
    }

    /**
     * @test
     */
    public function a_customer_can_add_a_product_with_a_quantity()
    {
        $customer = Customer::create([
            'name' => 'Customer Smith',
            'email' => 'customer@titan21.co.uk',
            'password' => Hash::make('password')
        ]);

        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        $this->assertEquals(1, Customer::count());
        $this->assertEquals(1, SimpleProduct::count());

        $customer->cart->add($product, 3);

        $this->assertEquals(1, $customer->cart->cartitems->count());
        $this->assertEquals(3, $customer->cart->cartitems->first()->quantity);
    }

    /**
     * @test
     */
    public function a_customer_can_remove_a_product_by_product_id()
    {
        $customer = Customer::create([
            'name' => 'Customer Smith',
            'email' => 'customer@titan21.co.uk',
            'password' => Hash::make('password')
        ]);

        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        $this->assertEquals(1, Customer::count());
        $this->assertEquals(1, SimpleProduct::count());

        $customer->cart->add($product);

        $this->assertEquals(1, $customer->cart->cartitems->count());

        $customer->cart->remove($product->id);
        $customer->refresh();

        $this->assertEquals(0, $customer->cart->cartitems->count());
    }

    /**
     * @test
     */
    /**
     * @test
     */
    public function a_customer_can_remove_a_product_by_product_id_with_a_quantity()
    {
        $customer = Customer::create([
            'name' => 'Customer Smith',
            'email' => 'customer@titan21.co.uk',
            'password' => Hash::make('password')
        ]);

        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        $this->assertEquals(1, Customer::count());
        $this->assertEquals(1, SimpleProduct::count());

        $customer->cart->add($product, 5);

        $this->assertEquals(1, $customer->cart->cartitems->count());
        $this->assertEquals(5, $customer->cart->cartitems->first()->quantity);

        $customer->cart->remove($product->id, 2);
        $customer->refresh();

        $this->assertEquals(1, $customer->cart->cartitems->count());
        $this->assertEquals(3, $customer->cart->cartitems->first()->quantity);
    }

    /**
     * @test
     */
    public function a_customer_can_clear_a_cart()
    {
        $customer = Customer::create([
            'name' => 'Customer Smith',
            'email' => 'customer@titan21.co.uk',
            'password' => Hash::make('password')
        ]);

        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        $this->assertEquals(1, Customer::count());
        $this->assertEquals(1, SimpleProduct::count());

        $customer->cart->add($product, 5);

        $this->assertEquals(1, $customer->cart->cartitems->count());
        $this->assertEquals(5, $customer->cart->cartitems->first()->quantity);

        $customer->cart->clear();
        $customer->refresh();

        $this->assertEquals(0, $customer->cart->cartitems()->count());
    }

    /**
     * @test
     */
    public function a_customer_can_get_the_subtotal()
    {
        $customer = Customer::create([
            'name' => 'Customer Smith',
            'email' => 'customer@titan21.co.uk',
            'password' => Hash::make('password')
        ]);

        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        $product2 = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '150'
        ]);

        $customer->cart->add($product);
        $customer->cart->add($product2, 2);

        $this->assertEquals(2300, $customer->cart->getSubtotal());
    }

    /**
     * @test
     */
    public function a_customer_can_get_the_subtotal_where_price_is_overriden()
    {
        $customer = Customer::create([
            'name' => 'Customer Smith',
            'email' => 'customer@titan21.co.uk',
            'password' => Hash::make('password')
        ]);

        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        $complex = ComplexProduct::create([
            'name' => 'A Simple Product',
            'width' => '10',
            'height' => '20'
        ]);

        $customer->cart->add($product);
        $customer->cart->add($complex, 2);

        $this->assertEquals(2400, $customer->cart->getSubtotal());
    }

    /**
     * @test
     */
    public function the_cart_will_state_whether_a_product_is_in_the_cart()
    {
        $customer = Customer::create([
            'name' => 'Customer Smith',
            'email' => 'customer@titan21.co.uk',
            'password' => Hash::make('password')
        ]);

        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        $complex = ComplexProduct::create([
            'name' => 'A Simple Product',
            'width' => '10',
            'height' => '20'
        ]);

        $customer->cart->add($complex, 2);
        //$customer->cart->add($product);

        $this->assertTrue($customer->cart->isInCart(ComplexProduct::class, $complex->id));
        $this->assertFalse($customer->cart->isInCart(SimpleProduct::class, $product->id));
    }
}
