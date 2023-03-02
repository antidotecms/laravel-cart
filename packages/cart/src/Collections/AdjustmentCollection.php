<?php

namespace Antidote\LaravelCart\Collections;

use Antidote\LaravelCart\Models\Adjustment;
use Antidote\LaravelCart\Models\OrderAdjustment;
use Illuminate\Database\Eloquent\Collection;

class AdjustmentCollection extends Collection
{
    public function appliedToSubtotal()
    {
        return $this->filter(function (OrderAdjustment|Adjustment $adjustment) {
            return $adjustment->apply_to_subtotal;
        });
    }

    public function appliedToTotal()
    {
        return $this->filter(function (OrderAdjustment|Adjustment $adjustment) {
            return !$adjustment->apply_to_subtotal;
        });
    }

    public function valid()
    {
        return $this->filter(function (OrderAdjustment|Adjustment $adjustment) {
            return $adjustment->is_valid;
        });
    }

    public function active()
    {
        return $this->filter(function (OrderAdjustment|Adjustment $adjustment) {
            return $adjustment->is_active;
        });
    }
}
