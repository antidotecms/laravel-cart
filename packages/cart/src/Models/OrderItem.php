<?php

namespace Antidote\LaravelCart\Models;

use Antidote\LaravelCart\Concerns\ConfiguresOrderItem;
use Antidote\LaravelCart\Database\Factories\OrderItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use ConfiguresOrderItem;
    use HasFactory;

    protected static function newFactory()
    {
        return OrderItemFactory::new();
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(getClassNameFor('product'), 'product_id');
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
