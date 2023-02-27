<?php

namespace Antidote\LaravelCart\Contracts;

use Antidote\LaravelCart\Collections\AdjustmentCollection;
use Antidote\LaravelCart\Concerns\ConfiguresAdjustment;
use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    use ConfiguresAdjustment;

    public function newCollection(array $models = [])
    {
        return new AdjustmentCollection($models);
    }
}
