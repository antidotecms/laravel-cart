<?php

namespace Antidote\LaravelCartFilament\Resources;

use Antidote\LaravelCartFilament\CartPanelPlugin;
use Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\CreateCustomer;
use Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\EditCustomer;
use Antidote\LaravelCartFilament\Resources\CustomerResource\Pages\ListCustomers;
use Antidote\LaravelCartFilament\Resources\CustomerResource\RelationManagers\OrderRelationManager;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationGroup = 'Orders';

    public static function getModel(): string
    {
        return CartPanelPlugin::get('models.customer');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('name')
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id'),
                TextInput::make('name'),
                TextInput::make('email')
                    ->email()
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

    public static function getRelations(): array
    {
        return [
            OrderRelationManager::class
        ];
    }
}
