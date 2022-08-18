<?php

namespace Antidote\LaravelCart\DataTransferObjects\Casters;

use Antidote\LaravelCart\DataTransferObjects\CartItem;
use Exception;
use Spatie\DataTransferObject\Caster;

class CartItemsCollectionCaster implements Caster
{

    public function cast(mixed $value): mixed
    {
        if (!is_array($value)) {
            throw new Exception("Can only cast arrays to CartItems");
        }

        return collect(array_map(
            fn(array $data) => new CartItem(...$data),
            $value
        ));
    }
}
