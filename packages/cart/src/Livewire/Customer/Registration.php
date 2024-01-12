<?php

namespace Antidote\LaravelCart\Livewire\Customer;

use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Registration extends Component implements HasForms
{
    use InteractsWithForms;

    //public ?array $details = [];
    //public ?array $address = [];

    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Your Details')
                ->schema([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('email')
                        ->required()
                        ->email()
                        ->unique(),
                    TextInput::make('password')
                        ->password()
                        ->required()
                        ->confirmed(),
                    TextInput::make('password_confirmation')
                        ->password()
                ]),
            Section::make('Address')
                ->relationship('address')
                ->schema([
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
            ]),
            Actions::make([
                Actions\Action::make('register')
                    ->action(fn() => $this->register())
            ])

        ])
        ->statePath('data')
        ->model(Customer::class);
    }

    public function register()
    {
        //$this->form->validate();

        //dd($this->form->getState());

        $password = Hash::make($this->form->getState()['password']);

        $state = array_merge($this->form->getState(), ['password' => $password]);

        $customer = Customer::create($state);

        $this->form->model($customer)->saveRelationships();

        //$customer->sendEmailVerificationNotification();
        event(new Registered($customer));

        Auth::guard('customer')->login($customer);

        $this->redirect(CartPanelPlugin::get('urls.dashboard'));
    }

    public function render()
    {
        return view('laravel-cart::livewire.customer.registration');
    }
}
