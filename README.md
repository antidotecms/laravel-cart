# Laravel Cart
A simple implementation of a cart for use in Laravel applications. Very much a WIP!

## Products
Your product models should use the trait `isProduct` or `isVariableProduct` as well as implement `Product` or `VariableProduct`.

```
use \Anitidote\LaravelCart\Contracts\Product;
use \Anitidote\LaravelCart\Concerns\IsProduct;
use Illuminate\Database\Eloquent\Model;

class MyProduct extends Model implements Product
{
    use isProduct;

    ...
}
```
Products can also be "Variable" when the price is determined by factors at run time:
```
use Antidote\LaravelCart\Contracts\VariableProduct
use Antidote\LaravelCart\Concerns\IsProduct;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements VariableProduct
{
    use IsVariableProduct;

    ...
}
```

By default, the cart will use the `name`, `description` (intended as a short
description of your product - useful for variable products or products that
use a calculation in their pricing) and `price` attributes on your model.
If you do not have these attributes or wish to modify them, you can
override `getName`, `getDescription` and `getPrice` in your model.

```
use \Anitidote\LaravelCart;

class MyProduct extends Model implements Product
{
    use isProduct;

    public function getName() : string
    {
        return $this->title;
    }
    
    public function getDescription() : string
    {
        return $this->info;
    }
    
    public function getPrice() : int
    {
        return $this->amount;
    }
    
}
```

```
use \Anitidote\LaravelCart;

class MyProduct extends Model implements VariableProduct
{
    use isVariableProduct;

    public function getName(?array $specification) : string
    {
        return 'A {$specification['color']} sweater';
    }
    
    public function getDescription(?array $specification) : string
    {
        return 'Made from {$specification['color']} wool';
    }
    
    public function getPrice(?array $specification) : int
    {
        return match($specification['color']) {
            'blue' => '100',
            'red' => 80'
        };
    }
    
}
```

## Cart
Your user models should use the `HasCart` trait which will create a cart automatically
for a user when accessed for the first time.

### Common Cart Methods

```

use Antidote\LaravelCart\Facades\Cart;

Cart::add($product); //add a product
Cart::add($product, 2); //add 2 products
Cart::($variable_product, 1, ['width' => 10, 'height' => 2]); // add a variable product

Cart::remove($product); //remove a product by id irrespective of quantity. If a varaible product, it will remove irrespective of specification
Cart::remove($product); //remove one product by id
Cart::remove($product, 2); //remove two products by id
Cart::remove($variable_product, 1, $specification); //remove a product by a specification

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
