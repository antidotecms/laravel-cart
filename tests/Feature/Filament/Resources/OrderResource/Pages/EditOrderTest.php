<?php

beforeEach(function() {

    $this->product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct()->create();
    $this->customer = \Antidote\LaravelCart\Models\Customer::factory()->create();
    $this->orders = \Antidote\LaravelCart\Models\Order::factory()
        ->count(10)
        ->withProduct($this->product)
        ->forCustomer($this->customer)
        ->create([
            'status' => 'an order status'
        ]);

    $this->user = \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestUser::create([
        'name' => 'Test User',
        'email' => 'test@user.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);

});

//@todo only when order is in a completed state
it('will allow sending an order confirmation mail again', function() {

    \Illuminate\Support\Facades\Event::fake();

    \Pest\Livewire\livewire(\Antidote\LaravelCartFilament\Resources\OrderResource\Pages\EditOrder::class, [
        'record' => $this->orders->first()->getKey()
    ])
        ->callAction('resend_order_complete_notification');

    \Illuminate\Support\Facades\Event::assertDispatched(\Antidote\LaravelCart\Events\OrderCompleted::class);
})
->coversClass(\Antidote\LaravelCartFilament\Resources\OrderResource\Pages\EditOrder::class);
