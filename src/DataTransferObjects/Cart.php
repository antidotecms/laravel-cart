<?php

namespace Antidote\LaravelCart\DataTransferObjects;

use Antidote\LaravelCart\DataTransferObjects\Casters\CartItemsCollectionCaster;
use Illuminate\Support\Collection;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\DataTransferObject;

class Cart extends DataTransferObject
{
    #[CastWith(CartItemsCollectionCaster::class)]
    public Collection | null $cart_items;
}
