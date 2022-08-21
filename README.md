# Laravel Cart
A simple implementation of a cart for use in Laravel applications. Very much a WIP!

## Products
Your product models should use the trait `Antidote\LaravelCart\Concerns\IsProduct` as well as implement
`\Antidote\LaravelCart\Contracts\Product`.

```
namespace \App\Models\Products;

use \Anitidote\LaravelCart\Contracts\Product;
use \Anitidote\LaravelCart\Concerns\IsProduct;
use Illuminate\Database\Eloquent\Model;

class MyProduct extends Model implements Product
{
    use isProduct;

    ...
}
```

Products are associated with a `ProductDataType` which encapsulates the nature of the product. For example, a
`ClothesProductType` may need to store information about the colour and size of the product to be purchased.
This association is done at run time so that only one `Product` model is needed without instead of many different Product 
models for products that have different behaviours

```
namespace \App\Products\Models\ProductDataType;

class ClothesDataType extends Model
{
    use IsProductDataType;

```

By default, the products will get the `name`, `description` (intended as a short description of your product)
and `price` attributes on your model. If you do not have these attributes or wish to modify them, you can
override `getName`, `getDescription` and `getPrice` in your model.

```
use \Anitidote\LaravelCart;

class MyProduct extends Model implements Product
{
    use isProduct;

    public function getName(?array $product_data = null) : string
    {
        return $this->title;
    }
    
    public function getDescription(?array $product_data = null) : string
    {
        return $this->info;
    }
    
    public function getPrice(?array $product_data = null) : int
    {
        return $this->amount;
    }
    
}
```

If you need to determine these dynamically, you can pass in `product_data` to allow customization any of the
attributes.

```

class Product extends Model implments Product
{
    use IsProduct;
}
```
```
class AmazingSquaresProductDataType extends Model
{
    use IsProductType;
    
    public function getPrice(?array $product_data) : int
    {
        return $product_data['width'] * $product_data['height'];
    }
}
```
```
$product_data = [
    'width' => 100,
    'height' => 100`
];

$product = $product->create();
$amazing_squares_product_data_type = AmazingSquaresProductDataType::create();

$product->productDataType->associate($amazing_squares_product_data_type);
$product->save();

$price = $product->getPrice($product_data);  // 1000

```

## Cart
The `Cart` facade is session based and provides the following methods

### Cart Methods

```

use Antidote\LaravelCart\Facades\Cart;

Cart::items(); //returns a collection of the items in the cart

Cart::add($product); //add a product
Cart::add($product, 2); //add 2 products

// add a product with product_data. If an item in the cart
already has the same product_data, the quantity is increased
by one, otherwise a new cartitem is added.
Cart::($variable_product, 1, $product_data); 

Cart::remove($product); //remove a product by id irrespective of quantity.
Cart::remove($product); //remove one product by id
Cart::remove($product, 2); //remove two products by id
Cart::remove($variable_product, 1, $product_data); //remove a product with teh specified product_data

Cart::clear(); //empty the cart

Cart::getSubtotal(); //return the subtotal of the cart
Cart::getTotal(); //returns the total of the cart including any adjustments

Cart::isInCart(BookProduct::class, 23); //determine if a particular product type with id 23  is in the cart
```

## Discounts
You can also create discounts for the cart. `CartAdjustments` are persisted and can be set to active along
with an associated `Discount` where the logic for determining the amount of the discount and its validity.

```
CartAdjustment::create([
    'name' => '10% off new Hannibal Lector book',
    'class' => SpecialDiscount::class,
    'parameters' => [
        'percentage' => 10
    ],
    'active' => true
]);
```

`Discounts` are not persisted to the DB and serve as a place to implement your own logic. You can make
them flexible enough to allow customization by end users. Create a simple class that extends from the
abstract class `Discount` and implement `amount()` and `isValid()`. The class provides the instance of
the currently `auth`'ed users `cart` and the `adjustment` details  

```
use Antidote\LaravelCart\Abstracts\Discount;
use Antidote\LaravelCart\Facades\Cart;

class SpecialDiscount implements Discount
{
    public function amount(): int
    {
        return Cart::getSubtotal() * ($this->adjustment->parameters['percentage']/100);
    }

    public function isValid(): bool
    {
        Cart::isInCart(BookProduct::class, 23);
    }
}

```

# 
