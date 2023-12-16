<?php

namespace Antidote\LaravelCart\Tests\Fixtures\factories\Products;

use Antidote\LaravelCart\Models\Products\SimpleProductType;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Products\TestProduct;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\ProductTypes\ComplexProductDataType;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\ProductTypes\InvalidSimpleProductDataType;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\ProductTypes\VariableProductDataType;

class TestProductFactory extends \Illuminate\Database\Eloquent\Factories\Factory
{
    protected $model = TestProduct::class;

    public function definition() {
        return [
            'name' => $this->faker->sentence
        ];
    }

    public function asSimpleProduct(array $definition = []): static
    {
        return $this->afterCreating(function($product) use ($definition) {

            $definition = array_merge([
                'price' => rand(50, 1000)
            ], $definition);
            //@todo create factory and remove nullable from field in migration
            //$simple_product_type = SimpleProductDataType::create($definition);
            $simple_product_type = SimpleProductType::create($definition);
            $product->productType()->associate($simple_product_type);
            $product->save();

        });
    }

    public function asInvalidSimpleProduct(array $definition = [])
    {
        return $this->afterCreating(function($product) use ($definition) {

            $definition = array_merge([
                'price' => rand(50, 1000)
            ], $definition);
            //@todo create factory and remove nullable from field in migration
            $simple_product_type = InvalidSimpleProductDataType::create($definition);
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
