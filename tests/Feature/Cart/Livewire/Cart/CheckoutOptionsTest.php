<?php

it('will display a form with checkout option buttons', function() {

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\CheckoutOptions::class)
        ->assertFormExists2('form', function(\Filament\Forms\Form $form) {

            /** @var \Filament\Forms\Components\Actions $actions */
            $actions = collect($form->getFlatComponents())->filter(fn($component) => get_class($component) == \Filament\Forms\Components\Actions::class)->first();

            /** @var \Filament\Forms\Components\Actions\Action $action */
            $action = collect($actions->getChildComponents())->first()->getAction('Stripe');

            expect($action->getLabel() == 'Checkout with Stripe');

            return true;

        })
        ->assertSee('Checkout with Stripe');
})
->covers(\Antidote\LaravelCart\Livewire\Cart\CheckoutOptions::class);

// @todo messy - need proper assertions to verify an action given in an "Actions" group
it('will navigate to the checkout page', function () {

    $this->product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create([
        'name' => 'A Very Simple Product'
    ]);

    $this->cart = app(\Antidote\LaravelCart\Domain\Cart::class);
    $this->cart->add($this->product, 1, []);

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $this->be($customer, 'customer');

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\CheckoutOptions::class)
//        ->assertFormExists2('form', function(\Filament\Forms\Form $form) {
//
//            /** @var \Filament\Forms\Components\Actions $actions */
//            $actions = collect($form->getFlatComponents())->filter(fn($component) => get_class($component) == \Filament\Forms\Components\Actions::class)->first();
//
//            /** @var \Filament\Forms\Components\Actions\Action $action */
//            $action = collect($actions->getChildComponents())->first()->getAction('Stripe');
//
//            //return $action->call();
//
//            return true;
//        })
        ->tap(function($livewire) {

            //dd($livewire->instance());

            /** @var \Filament\Forms\Components\Actions $actions */
            $actions = collect($livewire->instance()->form->getFlatComponents())->filter(fn($component) => get_class($component) == \Filament\Forms\Components\Actions::class)->first();

            /** @var \Filament\Forms\Components\Actions\Action $action */
            $action = collect($actions->getChildComponents())->first()->getAction('Stripe');

            /** @var \Illuminate\Http\RedirectResponse $response */
            $response = $action->call();
            //dump($response);

            expect($response)->toBeInstanceOf(\Illuminate\Http\RedirectResponse::class);
            //dump($response->isRedirect('http://127.0.0.1:8001/somewhere'));
            expect($response->getTargetUrl())->toEndWith('/checkout');

            expect(app(\Antidote\LaravelCart\Domain\Cart::class)->getActiveOrder())->not()->toBeNull();
        });
        //->assertRedirect('/somewhere');
})
->covers(\Antidote\LaravelCart\Livewire\Cart\CheckoutOptions::class);
