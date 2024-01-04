<?php

namespace Antidote\LaravelCart\Database\Factories;

use Antidote\LaravelCart\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'line_1' => $this->faker->address(),
            'line_2' => $this->faker->address(),
            'town_city' => $this->faker->city(),
            'county' => $this->faker->word(),
            'postcode' => $this->faker->postcode(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
