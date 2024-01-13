<?php

namespace Antidote\LaravelCart\Database\Factories;

use Antidote\LaravelCart\Models\PaymentData;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentDataFactory extends Factory
{
    protected $model = PaymentData::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->word,
            'value' => $this->faker->sentence
        ];
    }
}
