<div class='grid space-y-8'>

    <div class='space-y-4'>
        @forelse($this->cartItems as $key)
            <livewire:laravel-cart::cart.item :cartitem_id='$key' wire:key='cart_item_{{ $key }}'/>
        @empty
            <p>There are no items in the cart.</p>
        @endforelse
    </div>

    <x-laravel-cart::cart.totals/>
</div>
