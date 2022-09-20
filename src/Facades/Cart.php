<?php

namespace Antidote\LaravelCart\Facades;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection items()
 * @method static void add($product, int $quantity = 1, $product_data = null)
 * @method static void remove($product, $quantity, $product_data)
 * @method static void clear()
 * @method static int getSubtotal()
 * @method static int getTotal()
 * @method static int getDiscountTotal()
 * @method static bool isInCart($product)
 *
 * @see \Antidote\LaravelCart\Domain\Cart
 */

class Cart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cart';
    }
}
