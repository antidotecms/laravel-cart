<?php

namespace Antidote\LaravelCart\Contracts;

abstract class AdjustmentCalculation
{
    public abstract static function getFilamentFields(): array;
}
