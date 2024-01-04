<?php

namespace Antidote\LaravelCart\Livewire\Customer;

use Antidote\LaravelCart\Models\Customer;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class Address extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public Customer $customer;

    public function mount(): void
    {
        $this->customer = $this->getCustomer();
        $this->form->fill($this->customer->address->toArray());
    }

    private function getCustomer() : Customer
    {
        /** @var Customer $customer */
        $customer = auth('customer')->user();
        $customer->load('address');
        return $customer;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('line_1')
                ->required()
                ->hiddenLabel()
                ->placeholder('Line 1 (*)'),
            TextInput::make('line_2')
                ->hiddenLabel()
                ->placeholder('Line 2'),
            TextInput::make('town_city')
                ->required()
                ->hiddenLabel()
                ->placeholder('Town/City (*)'),
            TextInput::make('county')
                ->required()
                ->hiddenLabel()
                ->placeholder('County (*)'),
            TextInput::make('postcode')
                ->required()
                ->hiddenLabel()
                ->placeholder('Postcode (*)'),
            Actions::make([
                Action::make('Save')
                    ->action(fn() => $this->save())
            ])
        ])
            ->model($this->customer->address)
            ->statePath('data');
    }

    public function save()
    {
        $this->form->validate();

        $this->customer->address->update($this->form->getState());

        Notification::make()
            ->title('Address Updated')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('laravel-cart::livewire.customer.address');
    }
}
