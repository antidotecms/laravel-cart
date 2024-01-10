<?php

namespace Antidote\LaravelCart\Livewire\Customer;

use Antidote\LaravelCartFilament\CartPanelPlugin;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Menu extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public function logoutAction(): Action
    {
        return Action::make('logout')
            ->action(function() {
                Auth::logout();
                redirect(CartPanelPlugin::get('urls.customer').'/login');
            })
            ->visible(fn() => Auth::check('customer'));
    }

    public function loginAction()
    {
        return Action::make('login')
            ->action(function() {
                    redirect(CartPanelPlugin::get('urls.customer').'/login');
            })
            ->visible(fn() => !Auth::check('customer'));
    }

    public function homeAction()
    {
        return Action::make('home')
            ->action(function() {
                redirect(CartPanelPlugin::get('urls.customer').'/dashboard');
            })
            ->visible(fn() => Auth::check('customer'));

    }

    public function render()
    {
        return view('laravel-cart::livewire.customer.menu');
    }
}
