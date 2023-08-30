<?php

namespace Antidote\LaravelCart\Facades;

use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @codeCoverageIgnore
 * @see \Antidote\LaravelCart\Domain\Cart::add
 * @method static void add($product, int $quantity = 1, $product_data = null)
 * @see \Antidote\LaravelCart\Domain\Cart::items
 * @method static Collection items()
 * @see \Antidote\LaravelCart\Domain\Cart::clear
 * @method static void clear()
 * @see \Antidote\LaravelCart\Domain\Cart::remove
 * @method static void remove($product, $quantity, $product_data)
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
 * @see \Antidote\LaravelCart\Domain\Cart::getActiveOrder
 * @method static Order getActiveOrder()
 * @see \Antidote\LaravelCart\Domain\Cart::setActiveOrder
 * @method Order setActiveOrder(int|Order|null $order)
 * @see \Antidote\LaravelCart\Domain\Cart::addData
 * @method void addData(string $key, mixed $value)
 * @see \Antidote\LaravelCart\Domain\Cart::getData
 * @method mixed getData($key = null)
 * @see \Antidote\LaravelCart\Domain\Cart::getAdjustmentsTotal
 * @method int getAdjustmentsTotal(bool $applied_to_subtotal, array $except = [])
 * @see \Antidote\LaravelCart\Domain\Cart::getValidAdjustments
 * @method Collection getValidAdjustments(bool $applied_to_subtotal, array $except = [])
 */

class Cart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cart';
    }
}
