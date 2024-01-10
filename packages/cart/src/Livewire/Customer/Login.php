<?php

namespace Antidote\LaravelCart\Livewire\Customer;

use Antidote\LaravelCartFilament\CartPanelPlugin;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
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
                Section::make('Login')
                    ->schema([
                        ViewField::make('laravel-cart::components.error')
                            ->view('laravel-cart::errors')
                            ->visible(fn($livewire) => $livewire->getErrorBag()->get('fail')),
                        TextInput::make('email')
                            ->email()
                            ->required(),
                        TextInput::make('password')
                            ->required()
                            ->password(),
                        Actions::make([
                            Action::make('Login')
                                ->action(fn() => $this->login())
                        ])
                    ])
        ]);
    }

    public function login()
    {
        $this->form->validate();

        if(auth('customer')->attempt([
            'email' => $this->form->getState()['email'],
            'password' => $this->form->getState()['password']
        ])) {
            $this->redirect(session()->pull('url.intended', CartPanelPlugin::get('urls.dashboard')));
        } else {
            $this->addError('fail', 'no such user');
        }
    }

    public function render()
    {
        return view('laravel-cart::livewire.customer.login');
    }
}
