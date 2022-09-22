<?php

namespace Antidote\LaravelCart\Contracts;

use Antidote\LaravelCart\Concerns\HasFillableOrderItemAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

abstract class OrderItem extends Model
{
    use HasFillableOrderItemAttributes;

    public function product(): BelongsTo
    {
        $foreignKey = Str::snake(class_basename(config('laravel-cart.product_class'))) . '_id';
        return $this->belongsTo(config('laravel-cart.product_class'), $foreignKey);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getQuantity() : int
    {
        return $this->quantity;
    }

    public function getCost(): int
    {
        return $this->getPrice() * $this->getQuantity();
    }
}
