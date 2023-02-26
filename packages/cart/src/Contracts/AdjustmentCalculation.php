<?php

namespace Antidote\LaravelCart\Contracts;

interface AdjustmentCalculation
{
    /**
     * Returns the amount of discount to apply
     * @return int
     */
    public function calculatedAmount(array $parameters) : int;

    public function isValid() : bool;

    public function isActive() : bool;

    public function allowMultipleApplications() : bool;

    public function applyToSubtotal(): bool;
}
