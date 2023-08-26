<?php

namespace Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments;

use Antidote\LaravelCart\Contracts\AdjustmentCalculation;
use Antidote\LaravelCart\Facades\Cart;
use Filament\Forms\Components\TextInput;

class DiscountAdjustmentCalculation extends AdjustmentCalculation
{
    public function calculatedAmount(array $parameters) : int
    {
        $amount_affected = $this->applyToSubtotal()
            ? Cart::getSubtotal()
            : Cart::getTotal();

        return -(int) match($parameters['type']) {
            'percentage' => $amount_affected * ($parameters['rate']/100),
            'fixed' => $parameters['rate']
        };
    }

    public static function getFilamentFields(): array
    {
        return [
            TextInput::make('test_field')
        ];
    }

    public function isValid(): bool
    {
        return true;
    }

    public function applyToSubtotal(): bool
    {
        return true;
    }
}
