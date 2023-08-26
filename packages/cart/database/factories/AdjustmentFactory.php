<?php

namespace Antidote\LaravelCart\Database\Factories;

use Antidote\LaravelCart\Models\Adjustment;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\SimpleAdjustmentCalculation;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdjustmentFactory extends Factory
{
    protected $model = Adjustment::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function(Adjustment $adjustment) {

            if(!$adjustment->class) {
                $adjustment->class = rand(0, 1) ? DiscountAdjustmentCalculation::class : SimpleAdjustmentCalculation::class;
            }

            if(!$adjustment->parameters) {
                $adjustment->parameters = $adjustment->class == SimpleAdjustmentCalculation::class ? [] : [
                    'type' => rand(0, 1) ? 'percentage' : 'fixed',
                    'rate' => rand(10, 20)
                ];
            }
        });
    }
}
