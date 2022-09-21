<?php

namespace Database\Factories\Tests\Fixtures\app\Models\Products;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\Fixtures\app\Models\Products\TestCustomer;

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
