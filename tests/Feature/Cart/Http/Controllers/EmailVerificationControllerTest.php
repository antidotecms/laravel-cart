<?php

use Antidote\LaravelCartFilament\CartPanelPlugin;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

it('will mark an email as verified', function() {

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    expect($customer->email_verified_at)->toBeNull();

    $url = URL::temporarySignedRoute(
        'verification.verify',
        Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
        [
            'id' => $customer->getKey(),
            'hash' => sha1($customer->getEmailForVerification()),
        ]
    );

    $response = $this->actingAs($customer, 'customer')->get($url);

    expect($customer->email_verified_at)->not()->toBeNull();

    $response->assertRedirect(CartPanelPlugin::get('urls.dashboard'));
})
->covers(\Filament\Http\Controllers\Auth\EmailVerificationController::class);

it('will display a notice asking for email verification', function () {

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    expect($customer->email_verified_at)->toBeNull();

    $this->withoutExceptionHandling();

    $response = $this->actingAs($customer, 'customer')->get('/customer'.CartPanelPlugin::get('urls.dashboard'));

    $response->assertRedirect(route('verification.notice'));
})
->covers(\Filament\Http\Controllers\Auth\EmailVerificationController::class);

it('will resend an email verification email', function () {

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    \Illuminate\Support\Facades\Notification::fake();

    $this->actingAs($customer, 'customer')->get(route('verification.send'));

    \Illuminate\Support\Facades\Notification::assertSentTo($customer, \Illuminate\Auth\Notifications\VerifyEmail::class);
})
->covers(\Filament\Http\Controllers\Auth\EmailVerificationController::class);
