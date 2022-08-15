<?php

namespace Antidote\LaravelCart\Models;

use Illuminate\Database\Eloquent\Model;
use Antidote\LaravelCart\Contracts\Product;

class Cart extends Model
{
    public function customer()
    {
        return $this->morphTo();
    }

    public function cartitems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function add(Product $product, int $quantity = 1)
    {
        $cartitem = CartItem::updateOrCreate([
                'product_id' => $product->getKey(),
                'product_type' => get_class($product)
            ],[
                'cart_id' => $this->getKey(),
                'quantity' => $quantity
        ]);

        $this->cartitems()->save($cartitem);
    }

    /**
     * @param $product_id id of the product
     * @param $quantity number of products to remove. If null, all specified products are removed.
     * @return void
     */
    public function remove($product_id, $quantity = null) : void
    {
        if(!$quantity)
        {
            $this->removeAll($product_id);
        }
        else
        {
            $cartitem = $this->cartitems->where('product_id', $product_id)->first();
            $new_quantity = $cartitem->quantity - $quantity;

            if($new_quantity <= 0)
            {
                $this->removeAll($product_id);
            }
            else
            {
                $cartitem->quantity = $new_quantity;
                $cartitem->save();
            }
        }

    }

    private function removeAll($product_id)
    {
        $this->cartitems->where('product_id', $product_id)->first()->delete();
    }

    /**
     * Clears the contents of the cart
     * @return void
     */
    public function clear()
    {
        $this->cartitems()->truncate();
    }

    public function getSubtotal()
    {
        $subtotal = 0;

        $this->cartitems->each(function($cartitem) use (&$subtotal)
        {
            $subtotal += $cartitem->product->getPrice() * $cartitem->quantity;
        });

        return $subtotal;
    }

    public function getTotal()
    {
        $total = $this->getSubtotal();

        $total -= $this->getDiscountTotal();

        return $total;
    }

    public function getDiscountTotal()
    {
        $discount_total = 0;

        CartAdjustment::all()->each(function($adjustment) use (&$discount_total)
        {
            $discount_total += $adjustment->isActive() && $adjustment->isValid() ? $adjustment->amount() : 0;
        });

        return $discount_total;
    }

    public function isInCart($product_classname, $product_id) : bool
    {
        return $this->cartitems()
            ->where('product_type', $product_classname)
            ->where('product_id', $product_id)
            ->exists();
    }
}
