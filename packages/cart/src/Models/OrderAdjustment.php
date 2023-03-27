<?php

namespace Antidote\LaravelCart\Models;

use Antidote\LaravelCart\Collections\AdjustmentCollection;
use Antidote\LaravelCart\Concerns\ConfiguresOrderAdjustment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAdjustment extends Model
{
    use ConfiguresOrderAdjustment;

    public function order() : BelongsTo
    {
        return $this->belongsTo(getClassNameFor('order'), 'order_id');
    }

    public function newCollection(array $models = [])
    {
        return new AdjustmentCollection($models);
    }
}
