<?php

beforeEach(function() {

    $this->product = \Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct::factory()->asSimpleProduct([
        'price' => 1999
    ])->create([
        'name' => 'A Very Simple Product'
    ]);

    $this->cart = app(\Antidote\LaravelCart\Domain\Cart::class);
    $this->cart->add($this->product, 1, []);
})
->covers(\Antidote\LaravelCart\Livewire\Cart\CartItem::class);

it('will populate the properies', function() {

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\CartItem::class, [
        'cartitem_id' => $this->cart->items()->keys()->first()
    ])
    ->assertSet('cartitemId', $this->cart->items()->keys()->first())
    ->assertSet('quantity', 1);
})
->covers(\Antidote\LaravelCart\Livewire\Cart\CartItem::class);

it('will display the line total', function () {

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\CartItem::class, [
        'cartitem_id' => $this->cart->items()->keys()->first()
    ])
    ->assertSet('lineTotal', '£19.99')
    ->assertSee('£19.99');
})
->covers(\Antidote\LaravelCart\Livewire\Cart\CartItem::class);

it('will display the product name', function () {

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\CartItem::class, [
        'cartitem_id' => $this->cart->items()->keys()->first()
    ])
        ->assertSet('productName', 'A Very Simple Product')
        ->assertSee('A Very Simple Product');
})
->covers(\Antidote\LaravelCart\Livewire\Cart\CartItem::class);

it('will display the product description', function () {

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\CartItem::class, [
        'cartitem_id' => $this->cart->items()->keys()->first()
    ])
        ->assertSet('productDescription', 'A description')
        ->assertSee('A description');
})
->covers(\Antidote\LaravelCart\Livewire\Cart\CartItem::class);

it('will display a quantity field', function () {

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\CartItem::class, [
        'cartitem_id' => $this->cart->items()->keys()->first()
    ])
    ->assertFormFieldExists('quantity', function(\Filament\Forms\Components\TextInput $field) {

        /* @var \Filament\Forms\Components\Actions\Action $suffix_action */
        $suffix_action = collect($field->getSuffixActions())->first();
        $valid_suffix_action = $suffix_action->getIcon() == 'heroicon-m-pencil-square' &&
            $suffix_action->isDisabled() &&
            $suffix_action->getSize() == \Filament\Support\Enums\ActionSize::Large;

        return $field->hasInlineLabel() &&
         $field->isRequired() &&
         $field->isNumeric() &&
         $field->getMinValue() == 1 &&
         $field->getStep() == 1 &&
         $field->isLive() &&
         $valid_suffix_action;
    });
})
->covers(\Antidote\LaravelCart\Livewire\Cart\CartItem::class);

it('will update the quanitity of a cart item', function () {

    expect(collect($this->cart->items())->first()->quantity)->toBe(1);

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\CartItem::class, [
        'cartitem_id' => $this->cart->items()->keys()->first()
    ])
        ->set('quantity', 2)
        ->assertFormFieldExists('quantity', function(\Filament\Forms\Components\TextInput $field) {

            /* @var \Filament\Forms\Components\Actions\Action $suffix_action */
            $suffix_action = collect($field->getSuffixActions())->first();
            $suffix_action->call();
            return true;
        })
        ->assertHasNoFormErrors();

    expect(collect($this->cart->items())->first()->quantity)->toBe(2);

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\CartItem::class, [
        'cartitem_id' => $this->cart->items()->keys()->first()
    ])
    ->assertSet('lineTotal', '£39.98')
        ->assertSee('£39.98');
})
->covers(\Antidote\LaravelCart\Livewire\Cart\CartItem::class);

it('will not update the quantiuty of a cart item if the quantity has not changed', function () {

    expect(collect($this->cart->items())->first()->quantity)->toBe(1);

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\CartItem::class, [
        'cartitem_id' => $this->cart->items()->keys()->first()
    ])
        ->assertFormFieldExists('quantity', function(\Filament\Forms\Components\TextInput $field) {

            /* @var \Filament\Forms\Components\Actions\Action $suffix_action */
            $suffix_action = collect($field->getSuffixActions())->first();
            $suffix_action->call();
            return true;
        })
        ->assertHasNoFormErrors();

    expect(collect($this->cart->items())->first()->quantity)->toBe(1);

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\CartItem::class, [
        'cartitem_id' => $this->cart->items()->keys()->first()
    ])
        ->assertSet('lineTotal', '£19.99')
        ->assertSee('£19.99');
});

it('will remove an item from the cart', function () {
    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Cart\CartItem::class, [
        'cartitem_id' => $this->cart->items()->keys()->first()
    ])
    ->assertFormFieldExists('quantity', function(\Filament\Forms\Components\TextInput $field) {

        /* @var \Filament\Forms\Components\Actions\Action $suffix_action */
        $suffix_action = collect($field->getSuffixActions())->last();
        $suffix_action->call();
        return true;
    })
    ->assertHasNoFormErrors();

    expect(count($this->cart->items()))->toBe(0);
})
->covers(\Antidote\LaravelCart\Livewire\Cart\CartItem::class);
