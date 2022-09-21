<?php

namespace Tests\Fixtures\app\Models\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tests\Fixtures\app\Models\ProductTypes\ComplexProductDataType;

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


    //these attributes are deferred to the product type
    protected array $product_data = [
        'price',
        'name'
    ];

    //the validity of the product is checked on the product type
    protected array $product_validity = [
        ComplexProductDataType::class
    ];

    public function getDescription() : string {
        return $this->description;
    }

    public function isValid() : bool {
        return true;
    }
}
