<?php

namespace Tests\Feature;

use Antidote\LaravelCart\Domain\Discount\PercentageDiscount;
use Antidote\LaravelCart\Facades\Cart;
use Antidote\LaravelCart\Models\CartAdjustment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Fixtures\app\Models\ComplexProduct;
use Tests\Fixtures\app\Models\Customer;
use Tests\Fixtures\app\Models\SimpleProduct;
use Tests\TestCase;

class DiscountTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_will_apply_a_discount()
    {
        $product = SimpleProduct::create([
            'name' => 'A Simple Product',
            'price' => '2000'
        ]);

        $percentage_discount = CartAdjustment::create([
            'name' => '10% off',
            'class' => PercentageDiscount::class,
            'parameters' => [
                'percentage' => 10
            ],
            'active' => true
        ]);

        Cart::add($product);

        $this->assertEquals(1800, Cart::getTotal());
    }
}
