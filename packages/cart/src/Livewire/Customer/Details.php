<?php

namespace Antidote\LaravelCart\Livewire\Customer;

use Antidote\LaravelCart\Models\Customer;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class Details extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public Customer $customer;

    public function mount(): void
    {
        $this->customer = $this->getCustomer();
        $this->form->fill($this->customer->toArray());
    }

    private function getCustomer() : Customer
    {
        /** @var Customer $customer */
        $customer = auth('customer')->user();
        return $customer;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required(),
            TextInput::make('email')
                ->required()
                ->disabled()
        ])
        ->model($this->customer)
        ->statePath('data');
    }

    public function save()
    {
        $this->form->validate();

        $this->customer->update($this->form->getState());

        $this->dispatch('detailsUpdated')->to(Dashboard::class);

        Notification::make()
            ->title('Details Updated')
            ->success()
            ->send();
    }

    public function render()
    {
        return view('laravel-cart::livewire.customer.details');
    }
}
