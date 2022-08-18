# Laravel Cart
A simple implementation of a cart for use in Laravel applications. Very much a WIP!

## Products
Your product models should use the trait `isProduct` as well as implement `Product`.

The relationship between a cart item and a product is polymorphic so continue to add
this trait if you have more than one model representing your products.

```
use \Anitidote\LaravelCart\Contracts\Product;
use \Anitidote\LaravelCart\Concerns\IsProduct;

class MyProduct implements Product
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
    use IsProduct;

    protected $fillable = [
        'name'
    ];
```

By default, the cart will use the `name` and `price` attributes on your
model. If you do not have these attributes, then you can override `getName`
and `getPrice` in your model.

```
use \Anitidote\LaravelCart;

class MyProduct implements Product
{
    use isProduct;

    public function getPrice() : int
    {
        return $this->amount;
    }
    
    public function getName() : string
    {
        return $this->title;
    }
}
```

`getPrice` can be useful if you need to set prices based on some dynamic factor.

```
public function getPrice() : int
{
    return $this->width * $this->height * 100;
}
```
For variable products, when the dynamic factors are only available at run time, `getPrice` requires
a 'specification':
```
public function getPrice(array $specification) : int
{
    return $specification['width'] * $specification['height'] * 100;
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

class SpecialDiscount implements Discount
{
    public function amount(): int
    {
        return $this->cart->getSubtotal() * ($this->adjustment->parameters['percentage']/100);
    }

    public function isValid(): bool
    {
        $this->cart->isInCart(BookProduct::class, 23);
    }
}

```
