<div class='space-y-8'>
    <span>

    </span>

    <div>
        <div class="flex flex-col">
            <span class="text-2xl font-bold mb-8">{{ $this->product->name }}</span>
            @if(isset($this->price))
            <span class="text-2xl">
                {{ $this->price }}
            </span>
            <span class="text-sm">plus VAT</span>
            @endif
        </div>
    </div>

    <form>
        {{ $this->form }}
    </form>

    <div class='flex items-center'>
        <div class='flex-2'>
        {{ $this->addToCartForm }}
        </div>

        <div class='flex-3'>
            <button wire:click="addToCart">Add To Cart</button>
        </div>
    </div>

    <x-filament-actions::modals />
</div>
