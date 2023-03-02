<?php

namespace Antidote\LaravelCart\Models;

use Antidote\LaravelCart\Concerns\ConfiguresOrderLogItem;

class OrderLogItem extends \Illuminate\Database\Eloquent\Model
{
    use ConfiguresOrderLogItem;

    public function order()
    {
        return $this->belongsTo(getClassNameFor('order'), 'order_id');
    }
}
