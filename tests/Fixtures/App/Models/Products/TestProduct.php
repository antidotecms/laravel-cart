<?php

namespace Antidote\LaravelCart\Tests\Fixtures\App\Models\Products;

use Antidote\LaravelCart\Tests\Fixtures\App\Models\ProductTypes\ComplexProductDataType;
use Antidote\LaravelCart\Tests\Fixtures\factories\Products\TestProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin Model
 * @property $name
 * @property $description
 * @property $price
 * @property $productDataType
 */
class TestProduct extends \Antidote\LaravelCart\Contracts\Product
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    protected static function newFactory()
    {
        return TestProductFactory::new();
    }


    //these attributes are deferred to the product type
    protected array $product_data = [
        'price',
        'name',
        'foo'
    ];

    //the validity of the product is checked on the product type
    protected array $product_validity = [
        ComplexProductDataType::class
    ];

    public function getDescription() : string {
        return $this->description ?? '';
    }

    public function isValid() : bool {
        return true;
    }
}
