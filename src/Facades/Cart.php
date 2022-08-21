<?php

namespace Antidote\LaravelCart\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method add($product, $quantity, $product_data)
 */

class Cart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cart';
    }
}
