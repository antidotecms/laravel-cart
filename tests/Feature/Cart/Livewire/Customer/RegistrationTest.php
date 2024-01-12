<?php

it('will register a new customer', function() {

    $customer  = \Antidote\LaravelCart\Models\Customer::factory()->make();
    $address = \Antidote\LaravelCart\Models\Address::factory()->make();

    $formData = array_merge(
        collect(array_merge($customer->toArray(), ['password' => 'password', 'password_confirmation' => 'password']))->mapWithKeys(fn($item, $key) => [$key => $item])->toArray(),
        collect($address->toArray())->mapWithKeys(fn($item, $key) => ['address.'.$key => $item])->toArray()
    );

    //dd($formData);

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Customer\Registration::class)
        ->fillForm($formData)
        ->call('register')
        ->assertHasNoFormErrors();

    expect(\Antidote\LaravelCart\Models\Customer::count())->toBe(1);
    expect(\Antidote\LaravelCart\Models\Address::count())->toBe(1);

    $customer->fresh();

    expect(\Antidote\LaravelCart\Models\Customer::first()->email_verified_at)->tobeNull();

    expect(\Antidote\LaravelCart\Models\Customer::first())->not()->toBeNull();
})
->covers(\Antidote\LaravelCart\Livewire\Customer\Registration::class);

it('will send out an email to confirm the email address given', function () {

    \Illuminate\Support\Facades\Event::fake();

    $customer  = \Antidote\LaravelCart\Models\Customer::factory()->make();
    $address = \Antidote\LaravelCart\Models\Address::factory()->make();

    $formData = array_merge(
        collect(array_merge($customer->toArray(), ['password' => 'password', 'password_confirmation' => 'password']))->mapWithKeys(fn($item, $key) => [$key => $item])->toArray(),
        collect($address->toArray())->mapWithKeys(fn($item, $key) => ['address.'.$key => $item])->toArray()
    );

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Customer\Registration::class)
        ->fillForm($formData)
        ->call('register')
        ->assertHasNoFormErrors();


    \Illuminate\Support\Facades\Event::assertDispatched(\Illuminate\Auth\Events\Registered::class, function (\Illuminate\Auth\Events\Registered $event) {
        return $event->user->id == \Antidote\LaravelCart\Models\Customer::first()->id;
    });
})
    ->covers(\Antidote\LaravelCart\Livewire\Customer\Registration::class);

it('will not allow registration with an email already taken', function () {

    $customer = \Antidote\LaravelCart\Models\Customer::factory()->create();

    $new_customer  = \Antidote\LaravelCart\Models\Customer::factory()->make();
    $address = \Antidote\LaravelCart\Models\Address::factory()->make();

    $formData = array_merge(
        collect(array_merge($customer->toArray(), ['password' => 'password', 'password_confirmation' => 'password', 'email' => $customer->email]))->mapWithKeys(fn($item, $key) => [$key => $item])->toArray(),
        collect($address->toArray())->mapWithKeys(fn($item, $key) => ['address.'.$key => $item])->toArray()
    );

    \Pest\Livewire\livewire(\Antidote\LaravelCart\Livewire\Customer\Registration::class)
        ->fillForm($formData)
        ->call('register')
        ->assertHasFormErrors(['email' => 'unique']);
})
->covers(\Antidote\LaravelCart\Livewire\Customer\Registration::class);;

