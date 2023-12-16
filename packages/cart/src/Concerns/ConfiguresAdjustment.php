<?php

namespace Antidote\LaravelCart\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property string $class
 * @property array $parameters
 * @property int $calculated_amount
 */

trait ConfiguresAdjustment
{
    use MapsPropertiesToAggregates;

    public function getTable()
    {
        return 'adjustments';
    }

    public function initializeConfiguresAdjustment()
    {
        $this->fillable[] = 'name';
        $this->fillable[] = 'class';
        $this->fillable[] = 'parameters';
        $this->fillable[] = 'apply_to_subtotal';
        $this->fillable[] = 'is_active';
    }

    public function getCasts() : array
    {
        return array_merge(parent::getCasts(), [
            'parameters' => 'array'
        ]);
    }

    protected function calculatedAmount() : Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->mapToAggregate($this->class, 'calculatedAmount', 0, $this->parameters)
        );
    }

    protected function isValid() : Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->mapToAggregate($this->class, 'isValid', true, [$this->parameters])
        );
    }
}
