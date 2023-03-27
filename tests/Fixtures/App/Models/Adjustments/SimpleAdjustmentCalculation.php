<?php

namespace Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments;

class SimpleAdjustmentCalculation extends \Antidote\LaravelCart\Contracts\AdjustmentCalculation
{
    public static function getFilamentFields(): array
    {
        return [];
    }
}
