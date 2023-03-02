<?php

namespace Antidote\LaravelCart\Models;

use Antidote\LaravelCart\Concerns\ConfiguresOrder;
use Antidote\LaravelCart\Contracts\OrderAdjustment;
use Antidote\LaravelCart\Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Order extends Model
{
    use ConfiguresOrder;
    use HasFactory;

    protected static function newFactory()
    {
        return OrderFactory::new();
    }

    public function items() : HasMany
    {
        return $this->hasMany(getClassNameFor('order_item'), 'order_id');
    }

    public function adjustments() : HasMany
    {
        return $this->hasMany(getClassNameFor('order_adjustment'), 'order_id');
    }

    public function customer() : BelongsTo
    {
        return $this->belongsTo(getClassNameFor('customer'), 'customer_id');
    }

    //@todo convert to attribute
    public function getSubtotal() : int
    {
        $subtotal = 0;

        $this->items()->each(function($order_item) use (&$subtotal) {
            $subtotal += $order_item->getCost();
        });

//        $this->adjustments()->where('is_in_subtotal', true)->each(function($order_adjustment) use (&$subtotal) {
//            //$order_adjustment->load('adjustment');
//            $subtotal += !$order_adjustment->adjustment->is_in_subtotal ?: $order_adjustment->amount;
//        });

        return $subtotal;
    }

    public function total() : Attribute
    {
        return Attribute::make(
            get: function($value) {
                $total = $this->getSubtotal();
                $total += $this->getAdjustmentTotal(false);
                $total += $this->getAdjustmentTotal(true);
                $total += (int) $this->tax;
                return $total;
            }
        );
    }

    public function tax() : Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return ceil(ceil(($this->getSubtotal() + $this->getAdjustmentTotal(true)) * config('laravel-cart.tax_rate')) * 100)/100;
            }
        );
    }

    public function getAdjustmentTotal(bool $is_in_subtotal)
    {
        $adjustment_total = 0;

        $this->load('adjustments');

//        $this->adjustments->each(function(OrderAdjustment $adjustment) use (&$adjustment_total, $is_in_subtotal) {
//            //$adjustment_total += $adjustment->adjustment->apply_to_subtotal == $is_in_subtotal ? $adjustment->amount : 0;
//            $adjustment_total += (new $adjustment->class)->applyToSubtotal() == $is_in_subtotal ? $adjustment->amount : 0;
//        });

        $this->getAdjustments($is_in_subtotal)
            ->each(function(OrderAdjustment $adjustment) use (&$adjustment_total, $is_in_subtotal) {
                $adjustment_total += $adjustment->amount;
            });

        return $adjustment_total;
    }

    public function getAdjustments(bool $is_in_subtotal)
    {
        return $this->adjustments->when($is_in_subtotal,
            fn($query) => $query->appliedToSubtotal(),
            fn($query) => $query->appliedToTotal()
        );
    }

    public function payment() : MorphTo
    {
        return $this->morphTo();
    }

    public function logItems() : hasMany
    {
        return $this->hasMany(getClassNameFor('order_log_item'), 'order_id');
    }

    public function log(string $message) : OrderLogItem
    {
        return $this->logItems()->create([
            'message' => $message
        ]);
    }

    public function updateStatus()
    {
        return null;
    }

    public function isCompleted()
    {
        return null;
    }
}
