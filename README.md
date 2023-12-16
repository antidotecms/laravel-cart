# Laravel Cart
A simple implementation of a cart for use in Laravel applications. Very much a WIP!

## Installation
<!--
### Database
The package only requires on table creating of `cart_adjustments`. The package will migrate this for you or you can
publish this to make amends using:
```
php artisan vendor:publish --tag=laravel-cart-migrations
```
### Configuration
By default, the package will use `\App\Models\Product` as your product model. You can override this by publishing
the config and setting the `product_data` config value:
```
php artisan vendor:publish --tag=laravel-cart-config
```
-->
### Configuiration
Configuration is handled via a Filament Panel Plugin which you should add to the relevant panel. You can also override the
default resources with your own implementation.
```php
use \Antidote\LaravelCartFilament\CartPanelPlugin;

public function panel(Panel $panel)
{
    return $panel
        ->plugin(
                CartPanelPlugin::make()
                    ->customerResource(MyCustomerResource::class)
                    ->orderResource(MyOrderresource::class)
                    ->adjustmentResource(MyAdjustmentResource::class)
            )
        ...
}
```

## Products
<!--
Your product models should use the trait `Antidote\LaravelCart\Concerns\ConfiguresProduct` as well as implement
`\Antidote\LaravelCart\Contracts\Product`.

```
namespace \App\Models\Products;

use \Anitidote\LaravelCart\Contracts\Product;
use \Anitidote\LaravelCart\Concerns\ConfiguresProduct;
use Illuminate\Database\Eloquent\Model;

class MyProduct extends Model implements Product
{
    use ConfiguresProduct;

    ...
}
```
-->

Product models shoudl extend from `\Antidote\LaravelCart\Models\Product`

Products are associated with a `ProductDataType` which encapsulates the nature of the product. For example, a
`ClothesProductType` may need to store information about the colour and size of the product to be purchased.
This association is done at run time so that only one `Product` model is needed without the need for many different Product 
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
// or $amazing_squares_product_data_type->product->save($product)

$price = $product->getPrice($product_data);  // 1000

```

### Product Types
Product Types are models that are related to the main Product model through a morph relationship. They should extend from
`\Antidote\LaravelCart\Cart\Contracts\ProductType`. You are free to add whatever fields are necessary to support the
product type and are required to provide a migration for it.

You are also required to provide two methods `isValid` and `getPrice` to determine validity and price. In addition, you must also provide a static
method of `form` which returns an array of fields to be used when creating or editing the product and product type in Filament.

You may optionally define a `getName` method to provide a name for more complex products which is dependent on
its configuration. For example, a "Blue Jumper" where you have a "Jumper" `Product` which needs to be qualified by its
colour.

If required, further methods can be added to a `Product` which can optionally defer their methods or properties to the underlying
`ProductType` object. Typically, this can be defined as:

```php
use Antidote\LaravelCart\Concerns\MapsToAggregates;

public function getWarehouseLocation(?array $product_data = []): string
{
    /** @var $this \Antidote\LaravelCart\Models\Product */
    return $this->mapToAggregate(
        aggregate: $this->productType,
        property_or_method: 'getWarehouseLocation',
        default: $this->warehouseLocation,
        params: $product_data
    );
```
The `mapToAggregate` method will look for a property or method in the aggregate and return that result. If the
property or method cannot be found, the default will be provided.

A class name can be passed as an aggregate in which case a new instance will be created and probed for
the property or method.


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
