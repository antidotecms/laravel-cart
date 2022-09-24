<?php

namespace Antidote\LaravelCart\Contracts;

use Antidote\LaravelCart\Concerns\ConfiguresOrderAdjustment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class OrderAdjustment extends Model
{
    use ConfiguresOrderAdjustment;

    public function order() : BelongsTo
    {
        $foreignKey = Str::snake(class_basename(config('laravel-cart.order_class'))) . '_id';
        return $this->belongsTo(config('laravel-cart.order_class'), $foreignKey);
    }

    public function amount()
    {
        $adjustment = App::make($this->class, ['adjustment' => $this]);
        return $adjustment->amount($this->order->getSubtotal());
    }
}
