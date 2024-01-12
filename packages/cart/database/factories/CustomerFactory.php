<?php

namespace Antidote\LaravelCart\Database\Factories;

use Antidote\LaravelCart\Models\Address;
use Antidote\LaravelCart\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => Hash::make('password'),
            'email_verified_at' => null,
            'remember_token' => Str::random(10),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function(Customer $customer) {
            Address::factory()->make([
                'customer_id' => $customer->id
            ]);
        })->afterCreating(function(Customer $customer) {
            Address::factory()->create([
                'customer_id' => $customer->id
            ]);
        });
    }
}
