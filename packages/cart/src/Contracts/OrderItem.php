<?php

namespace Antidote\LaravelCart\Contracts;

use Antidote\LaravelCart\Concerns\ConfiguresOrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

abstract class OrderItem extends Model
{
    use ConfiguresOrderItem;

    public function product(): BelongsTo
    {
        return $this->belongsTo(getClassNameFor('product'), getKeyFor('product'));
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
