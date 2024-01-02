<?php

namespace Antidote\LaravelCart\Models;

use Antidote\LaravelCart\Collections\AdjustmentCollection;
use Antidote\LaravelCart\Database\Factories\AdjustmentFactory;
use Antidote\LaravelCart\Models\Concerns\ConfiguresAdjustment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    use ConfiguresAdjustment;
    use HasFactory;

    protected static function newFactory()
    {
        return AdjustmentFactory::new();
    }

    public function newCollection(array $models = [])
    {
        return new AdjustmentCollection($models);
    }
}
