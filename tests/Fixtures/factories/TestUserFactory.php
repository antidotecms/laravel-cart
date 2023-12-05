<?php

namespace Antidote\LaravelCart\Tests\Fixtures\factories;

use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class TestUserFactory extends Factory
{
    protected $model = TestUser::class;
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => Hash::make('password')
        ];
    }
}
