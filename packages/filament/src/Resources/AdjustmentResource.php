<?php

namespace Antidote\LaravelCartFilament\Resources;

use Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\CreateAdjustment;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\EditAdjustment;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\ListAdjustments;
use Filament\FilamentManager;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdjustmentResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationGroup = 'Orders';

//    public static function getModel(): string
//    {
////        $classname = '\App\Models\Adjustment';
////        try {
////            $classname =  (string) getClassNameFor('adjustment');
////        } catch (\Exception $e) {}
////        finally {
////            return $classname;
////        }
//        return config('laravel-cart.classes.adjustment');
//    }

    public static function getModel(): string
    {
        //return config('laravel-cart.classes.adjustment');
        /** @var $filamentManager FilamentManager */
        $filamentManager = app('filament');
        $resource = $filamentManager->getPlugin('laravel-cart')->getModel('adjustment');
        return $resource;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name'),
                TextColumn::make('class'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
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
                Toggle::make('apply_to_subtotal')
                    ->default(true),
                    //@todo disable this if the value derives from code and not the DB
                    //->disabled()
                Toggle::make('is_active'),
                    //@todo disable this if the value derives from code and not the DB
                    //->disabled(),
                Section::make('Settings')
                    ->statePath('parameters')
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
