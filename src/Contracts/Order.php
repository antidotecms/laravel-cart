<?php

namespace Antidote\LaravelCart\Contracts;

use Antidote\LaravelCart\Concerns\ConfiguresOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

abstract class Order extends Model
{
    use ConfiguresOrder;

    public function items() : HasMany
    {
        return $this->hasMany(config('laravel-cart.orderitem_class'));
    }

    public function adjustments() : HasMany
    {
        return $this->hasMany(config('laravel-cart.order_adjustment_class'));
    }

    public function customer() : BelongsTo
    {
        $foreignKey = Str::snake(class_basename(config('laravel-cart.customer_class'))).'_id';
        return $this->belongsTo(config('laravel-cart.customer_class'), $foreignKey);
    }

    public function getSubtotal() : int
    {
        $subtotal = 0;

        $this->items()->each(function($cart_item) use (&$subtotal)
        {
            $subtotal += $cart_item->getCost();
        });

        return $subtotal;
    }

    public function getTotal()
    {
        $total = $this->getSubtotal();

        $total -= $this->getDiscountTotal();

        return $total;
    }

    public function getDiscountTotal()
    {
        $discount_total = 0;

        $this->adjustments()->each(function(OrderAdjustment $adjustment) use (&$discount_total) {
            $discount_total += $adjustment->amount();
        });

        return $discount_total;
    }
}
