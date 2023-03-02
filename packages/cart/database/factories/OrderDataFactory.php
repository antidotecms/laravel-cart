<?php

namespace Antidote\LaravelCart\Database\Factories;

use Antidote\LaravelCart\Models\OrderData;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDataFactory extends Factory
{
    protected $model = OrderData::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->word,
            'value' => $this->faker->sentence
        ];
    }
}
