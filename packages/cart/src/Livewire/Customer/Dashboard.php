<?php

namespace Antidote\LaravelCart\Livewire\Customer;

use Antidote\LaravelCart\Models\Customer;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public Customer $customer;

    public function mount(): void
    {
        $this->detailsUpdated();
    }

    #[On('detailsUpdated')]
    public function detailsUpdated()
    {
        $this->customer = $this->getCustomer();
        $this->form->fill($this->customer->toArray());
    }

    /** @codeCoverageIgnore  */
    #[Computed]
    public function name()
    {
        return $this->getCustomer()->name;
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
            \Filament\Forms\Components\Tabs::make()
                ->tabs([
                    Tab::make('Your Info')
                        ->schema([
                            ViewField::make('details')
                                ->view('laravel-cart::filament.fields.customer-details')
                        ]),
                    Tab::make('Your Delivery Address')
                        ->schema([

                        ])
                ]),
        ]);
    }

    public function render()
    {
        return view('laravel-cart::livewire.customer.dashboard');
    }
}
