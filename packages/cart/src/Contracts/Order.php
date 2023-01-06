<?php

namespace Antidote\LaravelCart\Contracts;

use Antidote\LaravelCart\Concerns\ConfiguresOrder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

abstract class Order extends Model
{
    use ConfiguresOrder;
    use HasFactory;

    public function items() : HasMany
    {
        return $this->hasMany(getClassNameFor('order_item'));
    }

    public function adjustments() : HasMany
    {
        return $this->hasMany(getClassNameFor('order_adjustment'));
    }

    public function customer() : BelongsTo
    {
        return $this->belongsTo(getClassNameFor('customer'), getKeyFor('customer'));
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

    public function total() : Attribute
    {
        return Attribute::make(
            get: function($value) {
                $total = $this->getSubtotal();
                $total -= $this->getDiscountTotal();
                $total += (int) $this->tax;
                return $total;
            }
        );
    }

    public function tax() : Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return ceil(($this->getSubtotal() - $this->getDiscountTotal()) * config('laravel-cart.tax_rate') * 100)/100;
            }
        );
    }

    public function getDiscountTotal()
    {
        $discount_total = 0;

        $this->adjustments()->each(function(OrderAdjustment $adjustment) use (&$discount_total) {
            $discount_total += $adjustment->amount();
        });

        return $discount_total;
    }

    public function payment() : MorphTo
    {
        return $this->morphTo();
    }

    public function logItems() : hasMany
    {
        return $this->hasMany(getClassNameFor('order_log_item'), getKeyFor('order'));
    }

    public function log(string $message) : OrderLogItem
    {
        return $this->logItems()->create([
            'message' => $message
        ]);
    }

    public abstract function updateStatus();
}
