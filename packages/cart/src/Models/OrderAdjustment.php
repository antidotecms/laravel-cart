<?php

namespace Antidote\LaravelCart\Models;

use Antidote\LaravelCart\Collections\AdjustmentCollection;
use Antidote\LaravelCart\Concerns\ConfiguresOrderAdjustment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;

class OrderAdjustment extends Model
{
    use ConfiguresOrderAdjustment;

    public function order() : BelongsTo
    {
        return $this->belongsTo(getClassNameFor('order'), 'order_id');
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

    public function newCollection(array $models = [])
    {
        return new AdjustmentCollection($models);
    }
}
