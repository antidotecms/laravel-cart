<?php

namespace Antidote\LaravelCart\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

abstract class Order extends Model
{
    public function items() : HasMany
    {
        return $this->hasMany(config('laravel-cart.orderitem_class'));
    }

    public function customer() : BelongsTo
    {
        $foreignKey = Str::snake(class_basename(config('laravel-cart.customer_class'))).'_id';
        return $this->belongsTo(config('laravel-cart.customer_class'), $foreignKey);
    }
}
