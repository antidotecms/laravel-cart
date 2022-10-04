<?php

namespace Antidote\LaravelCart\Tests\Browser\Stripe\Components;

use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestProduct;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Orchestra\Testbench\Factories\UserFactory;

class StripeTest extends \Antidote\LaravelCart\Tests\BrowserTestCase
{
    use InteractsWithViews;

    /**
     * @test
     */
    public function it_will_see_homepage()
    {
        $product = TestProduct::factory()->asSimpleProduct([
            'price' => 1000
        ])->create();

//        $customer = TestCustomer::factory()->create();
        $user = UserFactory::new()->create();
        //DB::commit();

        dump(env('APP_ENV'));

        $this->browse(function($browser) use ($user) {

            //$customer = TestCustomer::factory()->create();

            $browser
                //->actingAs($user, 'web')
                ->loginAs($user, 'web')
                //->assertAuthenticatedAs($user, 'web')
                ->visit('/')
                ->assertSee("home");
                //->assertUrlIs('http://127.0.0.1:8001/checkout');

//            dump(Auth::user());
//
//            //$browser->loginAs($customer);
//
//            Cart::add($product);
//
//
//            Cart::createOrder($customer);
//
//            PaymentIntent::fake();

            //$browser->pause(3000);

            //dd($browser);
        });
    }
}
