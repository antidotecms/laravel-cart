<?php

namespace Antidote\LaravelCart\Contracts;

abstract class AdjustmentCalculation
{
    //@tod does calclauted_amount need to be defined here as abstract to enforce developer to provide the method?
    public abstract static function getFilamentFields(): array;
}
