<?php

namespace Antidote\LaravelCart\Http\Controllers;

use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController
{
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect(CartPanelPlugin::get('urls.dashboard'));
    }

    public function notice()
    {
        return view(CartPanelPlugin::get('views.emailVerification'));
    }

    public function send(Request $request)
    {
        //dd($request);
        //dd($request->user());
        /** @var $user Customer */
        $user = $request->user();
        $user->sendEmailVerificationNotification();

        return back()->with('message', 'Verification link sent!');
    }
}
