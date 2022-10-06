<?php

namespace Antidote\LaravelCart\Tests\Browser\Stripe\Components;

use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;
use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestProduct;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\Facades\DB;
use Laravel\Dusk\Browser;

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

        $user = TestCustomer::factory()->create();
        //$user = UserFactory::new()->create();
        //dump(get_class($user)); // "Illuminate\Foundation\Auth\User"
        DB::commit();

        //dump(env('APP_ENV'));
        //dump($user);

        $this->browse(function(Browser $browser) use ($user) {

            //$customer = TestCustomer::factory()->create();

            //dump($user);

            $browser
                //->visit("_dusk/login/".$user->id."/web")
                //->actingAs($user, 'web')
                ->loginAs($user->id, 'web')
                //->assertAuthenticatedAs($user, 'web')
                //->tap(fn() => sleep(10))
                ->assertAuthenticatedAs($user, 'web')
                ->visit('/user')
                ->assertSee("hello");
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
