<?php

namespace Antidote\LaravelCart\Tests\laravel\database\factories\Products;

use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestCustomer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestCustomerFactory extends Factory
{
    protected $model = TestCustomer::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }
}
