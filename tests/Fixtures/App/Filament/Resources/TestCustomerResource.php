<?php

namespace Antidote\LaravelCart\Tests\Fixtures\App\Filament\Resources;

use Antidote\LaravelCartFilament\Resources\CustomerResource;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;

class TestCustomerResource extends CustomerResource
{
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id'),#
                TextInput::make('name'),
                TextInput::make('email')
                    ->email(),
                Textarea::make('address')
            ]);
    }
}
