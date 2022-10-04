<?php

//it('will communicate with Stripe', function() {
//
//    $product = \Tests\Fixtures\app\Models\Products\TestProduct::factory()->asSimpleproduct([
//        'price' => 1000
//    ])->create();
//
//    Cart::add($product);
//
//
//});

it('will access the homepage', function() {

    //$this->markTestIncomplete('set up skeleton app for testbench');

    //\Orchestra\Testbench\Dusk\Options::withoutUI();

    $this->browse(function($browser) {

        //dd($browser);
        $browser->visit('http://127.0.0.1:8001')
            ->tap(fn() => sleep(3))
            ->assertSee('Laravel');
    });
});
