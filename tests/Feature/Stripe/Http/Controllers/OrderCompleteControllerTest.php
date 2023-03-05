<?php

namespace Antidote\LaravelCart\Tests\Feature\Stripe\Http\Controllers;

use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Models\OrderItem;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;

class OrderCompleteControllerTest extends \Antidote\LaravelCart\Tests\TestCase
{
    /**
     * @test
     */
    public function it_will_allow_the_viewing_of_an_order_when_the_logged_in_user_is_the_customer()
    {
        $customer = Customer::factory()->create();

        $order = Order::factory()
            ->forCustomer($customer)
            ->create();

        $product = TestProduct::factory()->asSimpleProduct([
            'price' => 5000
        ])->create();

        OrderItem::factory()
            ->withProduct($product)
            ->forOrder($order)
            ->create();

        $response = $this->actingAs($customer, 'customer')
            ->get(config('laravel-cart.urls.order_complete').'?order_id='.$order->id)
            ->assertSuccessful();

    }
}
