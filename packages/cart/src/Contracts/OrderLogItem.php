<?php

namespace Antidote\LaravelCart\Contracts;

use Antidote\LaravelCart\Concerns\ConfiguresOrderLogItem;

abstract class OrderLogItem extends \Illuminate\Database\Eloquent\Model
{
    use ConfiguresOrderLogItem;

    public function order()
    {
        return $this->belongsTo(getClassNameFor('order'), getKeyFor('order'));
    }
}
