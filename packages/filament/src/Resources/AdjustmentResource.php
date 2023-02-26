<?php

namespace Antidote\LaravelCartFilament\Resources;

use Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\CreateAdjustment;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\EditAdjustment;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\ListAdjustments;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;

class AdjustmentResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Orders';

    public static function getModel(): string
    {
        $classname = '\App\Models\Adjustment';
        try {
            $classname =  (string) getClassNameFor('adjustment');
        } catch (\Exception $e) {}
        finally {
            return $classname;
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name'),
                TextColumn::make('class')
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                Select::make('class')
                    ->options(config('laravel-cart.adjustments'))
                    ->required()
                    ->reactive(),
                Toggle::make('Applies To Subtotal?')
                    ->disabled()
                    ->visible(fn($get) => $get('class'))
                    ->dehydrated(false)
                    ->afterStateHydrated(function($get, $component) {
                        $class = $get('class');
                        $component->state((new $class)->applyToSubtotal());
                    }),
                Section::make('Setttings')
                    ->visible(fn($get) => $get('class') != "")
                    ->schema(fn($get) => $get('class') != "" ? $get('class')::getFilamentFields() : [])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdjustments::route('/'),
            'create' => CreateAdjustment::route('/create'),
            'edit' => EditAdjustment::route('/{record}/edit'),
        ];
    }
}
