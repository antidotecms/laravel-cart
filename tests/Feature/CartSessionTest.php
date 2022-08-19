<?php

namespace Tests\Feature;

use Antidote\LaravelCart\DataTransferObjects\CartItem;
use Antidote\LaravelCart\Facades\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\MockObject\InvalidMethodNameException;
use Tests\Fixtures\app\Models\ComplexProduct;
use Tests\Fixtures\app\Models\Customer;
use Tests\Fixtures\app\Models\SimpleProduct;
use Tests\Fixtures\app\Models\VariableProduct;
use Tests\TestCase;

class CartSessionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_customer_can_add_a_product_as_a_guest()
    {
        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        Cart::add($product);

        $this->assertEquals(1, Cart::cartitems()->count());

        $product_data = new CartItem([
            'product_id' => $product->id,
            'product_type' => SimpleProduct::class,
            'product' => SimpleProduct::find($product->id),
            'quantity' => 1,
            'specification' => null
        ]);

        $this->assertEquals($product_data, Cart::cartitems()->first());
    }

    /**
     * @test
     */
    public function a_customer_can_add_a_product_with_a_quantity_as_a_guest()
    {
        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        $this->assertEquals(1, SimpleProduct::count());

        Cart::add($product, 3);

        $this->assertEquals(1, Cart::cartitems()->count());
        $this->assertEquals(3, Cart::cartitems()->first()->quantity);
    }

    /**
     * @test
     */
    public function a_customer_can_remove_a_product_by_product_id_as_a_guest()
    {
        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        $this->assertEquals(1, SimpleProduct::count());

        Cart::add($product);

        $this->assertEquals(1, Cart::cartitems()->count());

        Cart::remove($product);
        //$customer->refresh();

        $this->assertEquals(0, Cart::cartitems()->count());
    }

    /**
     * @test
     */
    public function customer_can_remove_a_product_with_its_specification()
    {
        $variable_product = VariableProduct::create([
            'name' => 'A variable product'
        ]);

        $variable_product2 = VariableProduct::create([
            'name' => 'Another variable product'
        ]);

        Cart::add($variable_product, 1, [
            'width' => 10,
            'height' => 10
        ]);

        Cart::add($variable_product2, 1, [
            'width' => 10,
            'height' => 20
        ]);

        $this->assertEquals(2, Cart::cartitems()->count());

        Cart::remove($variable_product, 1, [
            'width' => 10,
            'height' => 10
        ]);

        $this->assertEquals(1, Cart::cartitems()->count());

        $expected_product = new CartItem([
            'product_id' => $variable_product2->id,
            'product_type' => VariableProduct::class,
            'quantity' => 1,
            'specification' => [
                'width' => 10,
                'height' => 20
            ]
        ]);

        $this->assertEquals($expected_product, Cart::cartitems()->first());
    }

    /**
     * @test
     */
    public function a_customer_can_remove_a_product_by_product_id_with_a_quantity_as_a_guest()
    {
        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        $this->assertEquals(1, SimpleProduct::count());

        Cart::add($product, 5);

        $this->assertEquals(1, Cart::cartitems()->count());
        $this->assertEquals(5, Cart::cartitems()->first()->quantity);

        Cart::remove($product, 2);
        //$customer->refresh();

        $this->assertEquals(1, Cart::cartitems()->count());
        $this->assertEquals(3, Cart::cartitems()->first()->quantity);
    }

    /**
     * @test
     */
    public function a_customer_can_clear_a_cart()
    {
        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        $this->assertEquals(1, SimpleProduct::count());

        Cart::add($product, 5);

        $this->assertEquals(1, Cart::cartitems()->count());
        $this->assertEquals(5, Cart::cartitems()->first()->quantity);

        Cart::clear();

        $this->assertEquals(0, Cart::cartitems()->count());
    }

    /**
     * @test
     */
    public function a_customer_can_get_the_subtotal()
    {
        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        $product2 = SimpleProduct::create([
            'name' => 'A Second Simple Product',
            'price' => '150'
        ]);

        Cart::add($product);
        Cart::add($product2, 2);

        $this->assertTrue(Cart::isInCart(SimpleProduct::class, $product->id));
        $this->assertTrue(Cart::isInCart(SimpleProduct::class, $product2->id));

        $this->assertEquals(2300, Cart::getSubtotal());
    }

    /**
     * @test
     */
    public function a_customer_can_get_the_subtotal_where_price_is_overriden()
    {
        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        $complex = ComplexProduct::create([
            'name' => 'A Simple Product',
            'width' => '10',
            'height' => '20'
        ]);

        Cart::add($product);
        Cart::add($complex, 2);

        $this->assertEquals(2400, Cart::getSubtotal());
    }

    /**
     * @test
     */
    public function the_cart_will_state_whether_a_product_is_in_the_cart()
    {
        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        $complex = ComplexProduct::create([
            'name' => 'A Simple Product',
            'width' => '10',
            'height' => '20'
        ]);

        Cart::add($complex, 2);
        //Cart::add($product);

        $this->assertTrue(Cart::isInCart(ComplexProduct::class, $complex->id));
        $this->assertFalse(Cart::isInCart(SimpleProduct::class, $product->id));
    }

    /**
     * @test
     */
    public function a_customer_can_add_a_product_with_specifications()
    {
        $variable = VariableProduct::create([
            'name' => 'A variable product'
        ]);

        $specification = [
            'width' => 10,
            'height' => 10
        ];

        $this->assertEquals(100, $variable->getPrice($specification));

        Cart::add($variable, 1, [
            'width' => 10,
            'height' => 10
        ]);

        $this->assertTrue(Cart::isInCart(VariableProduct::class, $variable->id));
        $this->assertEquals(100, Cart::getSubtotal());
    }

    /**
     * @test
     */
    public function the_facade_will_throw_an_error_if_they_method_does_not_exist()
    {
        $this->expectException(\BadMethodCallException::class);

        Cart::nonExistantMethod();
    }

    /**
     * @test
     */
    public function products_will_return_the_correct_name_and_price()
    {
        $simple_product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '100'
        ]);

        $this->assertEquals('A Simple Product', $simple_product->getName());
        $this->assertEquals(100, $simple_product->getPrice());

        $complex_product = ComplexProduct::create([
            'name' => 'A Complex Product',
            'width' => 20,
            'height' => 10
        ]);

        $this->assertEquals('A Complex Product', $complex_product->getName());
        $this->assertEquals(200, $complex_product->getPrice());

        $specification = [
            'width' => 20,
            'height' => 10
        ];

        $variable_product = VariableProduct::create([
            'name' => 'A Variable Product'
        ]);

        $this->assertEquals('A Variable Product with width of 20 and height of 10', $variable_product->getName($specification));
        $this->assertEquals(200, $variable_product->getPrice($specification));
    }
}
