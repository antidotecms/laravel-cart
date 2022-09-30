<?php

namespace Antidote\LaravelCart\Facades;

use Antidote\LaravelCart\Contracts\Customer;
use Antidote\LaravelCart\Contracts\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @see \Antidote\LaravelCart\Domain\Cart::items
 * @method static Collection items()
 * @see \Antidote\LaravelCart\Domain\Cart::add
 * @method static void add($product, int $quantity = 1, $product_data = null)
 * @see \Antidote\LaravelCart\Domain\Cart::remove
 * @method static void remove($product, $quantity, $product_data)
 * @see \Antidote\LaravelCart\Domain\Cart::clear
 * @method static void clear()
 * @see \Antidote\LaravelCart\Domain\Cart::getSubtotal
 * @method static int getSubtotal()
 * @see \Antidote\LaravelCart\Domain\Cart::getTotal
 * @method static int getTotal()
 * @see \Antidote\LaravelCart\Domain\Cart::getDiscountTotal
 * @method static int getDiscountTotal()
 * @see \Antidote\LaravelCart\Domain\Cart::isInCart
 * @method static bool isInCart($product)
 * @see \Antidote\LaravelCart\Domain\Cart::createOrder
 * @method static Order createOrder(Customer $customer)
 * @see \Antidote\LaravelCart\Domain\Cart::initializePayment
 * @method initializePayment(Order $order)
 */

class Cart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cart';
    }
}
