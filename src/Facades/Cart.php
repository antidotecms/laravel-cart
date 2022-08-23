<?php

namespace Antidote\LaravelCart\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static items()
 * @method static add($product, int $quantity, $product_data)
 * @method static remove($product, $quantity, $product_data)
 * @method static clear()
 * @method static getSubtotal()
 * @method static getTotal()
 * @method static getDiscountTotal()
 * @method static isInCart($product)
 */

class Cart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cart';
    }
}
