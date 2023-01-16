<?php

namespace Antidote\LaravelCart\Tests\laravel\app\Filament\Resources;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use NumberFormatter;

class TestOrderResource extends \Antidote\LaravelCartFilament\Resources\OrderResource
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id')
                    ->disabled(),
                Select::make('customer')
                    ->relationship('customer', 'name'),
                TextInput::make('additional_field')
                    ->default('Additional field')
                    ->afterStateHydrated(fn($state) => 'Additional field'),
                Fieldset::make('Totals')
                    ->columns(1)
                    ->schema([
                        TextInput::make('order_subtotal')
                            ->afterStateHydrated(fn($component, $record) => $component->state(NumberFormatter::create('en_GB', NumberFormatter::CURRENCY)->formatCurrency($record->getSubtotal()/100, 'GBP')))
                            //->formatStateUsing(fn($state) => NumberFormatter::create('en_GB', NumberFormatter::CURRENCY)->formatCurrency($state/100, 'GBP'))
                            ->disabled(),
                        TextInput::make('tax')
                            ->afterStateHydrated(fn($component, $record) => $component->state(NumberFormatter::create('en_GB', NumberFormatter::CURRENCY)->formatCurrency($record->tax/100, 'GBP')))
                            ->disabled(),
                        TextInput::make('order_total')
                            ->afterStateHydrated(fn($component, $record) => $component->state(NumberFormatter::create('en_GB', NumberFormatter::CURRENCY)->formatCurrency($record->total/100, 'GBP')))
                            ->disabled(),
                    ]),
                TextInput::make('status')

            ]);
    }
}
