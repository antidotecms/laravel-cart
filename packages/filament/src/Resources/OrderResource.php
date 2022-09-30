<?php

namespace Antidote\LaravelCartFilament\Resources;

use App\Filament\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\Column;
use Illuminate\Support\Str;

class OrderResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Orders';

    public static function getModel(): string
    {
        return (string) Str::of(class_basename(getClassNameFor('order')))
                ->beforeLast('Resource')
                ->prepend('App\\Models\\');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Column::make('id'),
                Column::make('order_total')
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            //'edit' => Pages\EditProduct::route('/{record}/edit'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
