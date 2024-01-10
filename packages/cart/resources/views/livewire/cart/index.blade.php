<div class='grid space-y-8'>

        @forelse($this->cartItems as $key)
            <div class='space-y-4'>
                <livewire:laravel-cart::cart.item :cartitem_id='$key' wire:key='cart_item_{{ $key }}'/>
                <x-laravel-cart::cart.totals/>
                @auth('customer')
                    <livewire:laravel-cart::cart.checkout-options/>
                @endauth
                @guest('customer')
                    <p>To checkout, you need to <a href='{{ \Antidote\LaravelCartFilament\CartPanelPlugin::get('urls.login') }}'>login.</p>
                @endguest
            </div>
        @empty
            <p>There are no items in the cart.</p>
        @endforelse

</div>
