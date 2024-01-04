<a href='{{ $this->cartUrl }}'>
    <x-filament::icon-button
        icon="heroicon-m-shopping-cart"
        label="Mark notifications as read"
        color="black"
    >
        <x-slot name="badge">
            {{ count(app(\Antidote\LaravelCart\Domain\Cart::class)->items()) }}
        </x-slot>
    </x-filament::icon-button>
</a>
