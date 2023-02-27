<?php

namespace Antidote\LaravelCart\Contracts;

use Antidote\LaravelCart\Concerns\ConfiguresOrderAdjustment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;

abstract class OrderAdjustment extends Model
{
    use ConfiguresOrderAdjustment;

    public function order() : BelongsTo
    {
        return $this->belongsTo(getClassNameFor('order'), getKeyFor('order'));
    }

    public function calculatedAmount()
    {
        $adjustment = App::make($this->class, ['adjustment' => $this]);
        return $adjustment->calculatedmount($this->order->getSubtotal());
    }

//    public function adjustment()
//    {
//        return $this->belongsTo(config('laravel-cart.classes.adjustment'), getKeyFor('adjustment'));
//    }
}
