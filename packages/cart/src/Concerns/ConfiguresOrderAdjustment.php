<?php

namespace Antidote\LaravelCart\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait ConfiguresOrderAdjustment
{
    public function initializeConfiguresOrderAdjustment() : void
    {
        $this->fillable[] = 'name';
        $this->fillable[] = getKeyFor('adjustment');
        $this->fillable[] = getKeyFor('order');
        $this->fillable[] = 'amount';
        $this->fillable[] = 'original_parameters';
    }

    public function getCasts() : array
    {
        return array_merge(parent::getCasts(), [
            'original_parameters' => 'array'
        ]);
    }

    public function isValid(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->adjustment->is_valid
        );
    }

    public function isActive(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->adjustment->is_active
        );
    }

    public function applyToSubtotal(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->adjustment->apply_to_subtotal
        );
    }
}
