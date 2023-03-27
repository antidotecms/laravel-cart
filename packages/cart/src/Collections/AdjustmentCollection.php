<?php

namespace Antidote\LaravelCart\Collections;

use Antidote\LaravelCart\Models\Adjustment;
use Antidote\LaravelCart\Models\OrderAdjustment;
use Illuminate\Database\Eloquent\Collection;

/**
 * @todo
 * The argument to filter must take a Model as an argument. For static analsysis, we cannot use anything that subclasses
 * this. We can type hint the parameter in the body of the method:
 *
 * @\var OrderAdjustment $adjustment
 *
 * However we cannot type hint it twice. I.e:
 *
 * @\var OrderAdjustment|Adjustment $adjustment
 *
 * Need to extract methods into an interface so we can type hint the parameter with this interface
 *
 */
class AdjustmentCollection extends Collection
{
    public function appliedToSubtotal() : Collection
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
