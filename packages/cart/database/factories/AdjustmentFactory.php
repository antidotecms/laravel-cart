<?php

namespace Antidote\LaravelCart\Database\Factories;

use Antidote\LaravelCart\Models\Adjustment;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdjustmentFactory extends Factory
{
    protected $model = Adjustment::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence,
            'parameters' => []
        ];
    }
}
