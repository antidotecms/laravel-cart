<?php

namespace Antidote\LaravelCart\Livewire\Customer;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;

class Login extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
            TextInput::make('email')
                ->required(),
            TextInput::make('password')
                ->password()
        ]);
    }

    public function login()
    {
        $this->form->validate();

        if(auth('customer')->attempt([
            'email' => $this->form->getState()['email'],
            'password' => $this->form->getState()['password']
        ])) {
            $this->redirect('/yup');
        } else {
            $this->addError('fail', 'no such user');
        }
    }

    public function render()
    {
        return view('laravel-cart::livewire.customer.login');
    }
}
