<?php

namespace Antidote\LaravelCart\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use \Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'id',
        'product_id',
        'product_type',
        'cart_id',
        'quantity',
        'specification'
    ];

    protected $casts = [
        'specification' => 'array'
    ];

    public function product()
    {
        return $this->morphTo();
    }
}
