<?php

namespace Antidote\LaravelCartFilament\Resources;

use Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\CreateCustomer;
use Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\EditCustomer;
use Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\ListCustomers;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

class CustomerResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Orders';

    public static function getModel(): string
    {
        return (string) Str::of(class_basename(getClassNameFor('customer')))
            ->beforeLast('Resource')
            ->prepend('App\\Models\\');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name')
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }
}
