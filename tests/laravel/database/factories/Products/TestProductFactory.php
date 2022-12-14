<?php

namespace Antidote\LaravelCart\Tests\laravel\database\factories\Products;

use Antidote\LaravelCart\Tests\laravel\app\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\laravel\app\Models\ProductTypes\ComplexProductDataType;
use Antidote\LaravelCart\Tests\laravel\app\Models\ProductTypes\SimpleProductDataType;
use Antidote\LaravelCart\Tests\laravel\app\Models\ProductTypes\VariableProductDataType;

class TestProductFactory extends \Illuminate\Database\Eloquent\Factories\Factory
{
    protected $model = TestProduct::class;

    public function definition() {
        return [
            'name' => $this->faker->sentence
        ];
    }

    public function asSimpleProduct(array $definition = [])
    {
        return $this->afterCreating(function($product) use ($definition) {

            $definition = array_merge([
                'price' => rand(50, 1000)
            ], $definition);
            $simple_product_type = SimpleProductDataType::create($definition);
            $product->productType()->associate($simple_product_type);
            $product->save();

        });
    }

    public function asComplexProduct(array $definition = [])
    {
        return $this->afterCreating(function($product) use ($definition) {

            $definition = array_merge([
                'width' => 10,
                'height' => 10
            ], $definition);
            $complex_product_type = ComplexProductDataType::create($definition);
            $product->productType()->associate($complex_product_type);
            $product->save();

        });
    }

    public function asVariableProduct(?array $definition = [])
    {
        return $this->afterCreating(function($product) use ($definition) {

            $definition = array_merge([], $definition);
            $variable_product_type = VariableProductDataType::create($definition);
            $product->productType()->associate($variable_product_type);
            $product->save();

        });
    }
}
