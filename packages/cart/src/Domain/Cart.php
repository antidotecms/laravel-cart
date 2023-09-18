<?php

namespace Antidote\LaravelCart\Domain;

use Antidote\LaravelCart\DataTransferObjects\CartItem;
use Antidote\LaravelCart\Models\Adjustment;
use Antidote\LaravelCart\Models\Customer;
use Antidote\LaravelCart\Models\Order;
use Antidote\LaravelCart\Models\OrderAdjustment;
use Antidote\LaravelCart\Models\Product;
use Antidote\LaravelCart\Types\ValidCartItem;
use Illuminate\Support\Collection;

class Cart
{
//    public function __call($method, $arguments) : mixed
//    {
//        method_exists($this, $method) ?: throw new \BadMethodCallException();
//
//        $allowedMethods = [
//            'add',
//            'items',
//            'clear',
//            'remove',
//            'getSubtotal',
//            'getTotal',
//            'getDiscountTotal',
//            'isInCart',
//            'createOrder',
//            'getActiveOrder',
//            'setActiveOrder',
//            'addData',
//            'getData',
//            'getAdjustmentsTotal',
//            'getValidAdjustments'
//        ];
//
//        in_array($method, $allowedMethods) ?: throw new \BadMethodCallException("Cannot call {$method}");
//
//        return $this->$method(...$arguments);
//    }

    public function items() : Collection
    {
        $cart_items = session()->get('cart_items') ?? [];
        $cart = new \Antidote\LaravelCart\DataTransferObjects\Cart(cart_items: $cart_items);

        return $cart->cart_items ?? collect([]);
    }

    public function add($product, int $quantity = 1, $product_data = null) : void
    {
        $cart_item = ValidCartItem::create(new CartItem([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'product_data' => $product_data
        ]));

        $cart_items = $this->items();

        $items = $cart_items
            ->where('product_id', '=', $product->id);

        if(!$items->count()) {
            $cart_items->push($cart_item);
        }
        else
        {

            $cart_items->filter(function($cart_item) use ($product, $product_data) {
                    return $cart_item->getProduct()->product_type_type == get_class($product->productType) &&
                        $cart_item->product_id == $product->id &&
                        $cart_item->product_data == $product_data;
                })
                ->whenNotEmpty(function($collection) use ($quantity) {
                    return $collection->map(function($cart_item) use ($quantity) {
                        //if($cart_item->product_data == $product_data) {
                            $cart_item->quantity +=  $quantity;
                        //}
                    });
                })
                ->whenEmpty(function($collection) use ($product, $quantity, $product_data, $cart_items) {

                    return $cart_items->push([
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'product_data' => $product_data
                    ]);
                });
        }

        $cart_items = $cart_items->toArray();

        session()->put('cart_items', $cart_items);
    }

    /**
     * @param $product mixed the product
     * @param $quantity int of products to remove. If null, all specified products are removed.
     * @return void
     */
    public function remove(Product $product, $quantity = null, $product_data = null) : void
    {
        $cart_items = $this->items();

        if($this->isInCart($product->id)) {

            //remove entirely
            $cart_items = $cart_items->reject(function($cart_item) use ($product, $quantity, $product_data) {

                $useProductData = !$product_data ?: $cart_item->product_data == $product_data;
                $useQuantity = !$quantity ?: $cart_item->quantity <= $quantity;

                return $cart_item->product_id == $product->id
                    && $useProductData
                    && $useQuantity;
            });

            //remove specific amount
            $cart_items->transform(function($cart_item) use ($product, $quantity, $product_data) {
                if($cart_item->product_id == $product->id
                    && (!$product_data ?: $cart_item->product_data == $product_data)) {
                    $cart_item->quantity -= $quantity;
                }

                return $cart_item;
            });


            $cart_items = $cart_items->reject(function($cart_item) {
                return $cart_item->quantity <= 0;
            });

            $this->clearOrderAdjustments($cart_items);

            session()->put('cart_items', $cart_items->toArray());
        }
    }

    private function clearOrderAdjustments(Collection $cart_items)
    {
        if((bool) $this->getActiveOrder() & !$cart_items->count()) {
            config('laravel-cart.classes.order_adjustment')::where('order_id', $this->getActiveOrder()->id)->delete();
            $order = $this->getActiveOrder()->fresh();
            $this->setActiveOrder($order);
        }
    }

    /**
     * Clears the contents of the cart
     * @return void
     */
    public function clear() : void
    {
        session()->put('cart_items', []);
    }

    public function getSubtotal() : int
    {
        $subtotal = 0;

        $this->items()->each(function($cart_item) use (&$subtotal)
        {
            $subtotal += $cart_item->getCost();
        });

        return $subtotal;
    }

    public function getTotal()
    {
        $total = $this->getSubtotal();

        $total += $this->getAdjustmentsTotal(true);
        $total += $this->getAdjustmentsTotal(false);

        return $total;
    }

    public function getAdjustmentsTotal(bool $applied_to_subtotal, array $except = [])
    {
        $discount_total = 0;

        $this->getValidAdjustments($applied_to_subtotal, $except)->each(function (OrderAdjustment|Adjustment $adjustment) use (&$discount_total) {
            $discount_total += is_a($adjustment, OrderAdjustment::class) ? $adjustment->amount : $adjustment->calculated_amount;
        });

        return $discount_total;
    }

    public function getValidAdjustments(bool $applied_to_subtotal, array $except = []) : Collection
    {
//        $class = $this->getActiveOrder() && $this->getActiveOrder()->isCompleted()
//            ? config('laravel-cart.classes.order_adjustment')
//            : config('laravel-cart.classes.adjustment');

        $class = config('laravel-cart.classes.adjustment');

        return $class::all()
            ->when($this->getActiveOrder(), function($query) {
                $query->where('order_id', $this->getActiveOrder()->id);
            })
            ->when($applied_to_subtotal,
                fn($query) => $query->appliedToSubtotal(),
                fn($query) => $query->appliedToTotal()
            )
            ->when(!empty($except),
                fn($query) => $query->whereNotIn('class',$except)
            )
            ->valid()
            ->active();

        //filter those that should or should not be applied to the subtotal
//            ->filter(function (OrderAdjustment|Adjustment $adjustment) use ($appled_to_subtotal) {
//
//                return $adjustment->apply_to_subtotal == $appled_to_subtotal;
//            })
        //filter those that are valid
//            ->filter(function (OrderAdjustment|Adjustment $adjustment) {
//                return $adjustment->is_valid;
//            })
        //filter those that are active
//            ->filter(function (OrderAdjustment|Adjustment $adjustment) {
//                return $adjustment->is_active;
//            });
    }

    public function isInCart($product_id) : bool
    {
        return $this->items()
            ->contains(function($cart_item) use ($product_id) {
                return $cart_item->product_id == $product_id;
            });
    }

    /**
     * @param Customer $customer
     * @return Order|bool a created or false if no order created
     * @throws \Exception
     */
    public function createOrder(Customer $customer) : Order | bool
    {
        if(Cart::getTotal() < 30 | Cart::getTotal() > 99999999) {
            throw new \Exception('The order total must be greater than £0.30 and less that £999,999.99');
        }

        $order = $this->getOrder($customer);

        $this->setOrderData($order);

        //$this->syncCartWithOrder($order);

        //$this->convertAdjustments($order);

        //$this->syncAdjustmentsWithOrder();
        $this->sync($order);

        $order->save();

        self::setActiveOrder($order);

        return $order;
    }

    private function syncCartWithOrder($order)
    {
        $order->items()->delete();

        $this->convertCartItemsToOrderItems($order);
    }

    private function syncAdjustmentsWithOrder($order)
    {
        $order->adjustments()->delete();

        $this->convertAdjustments($order);
    }

    private function syncDataWithOrder($order)
    {
        $data = $this->getData();
        foreach($data as $key => $value) {
            $order->setData($key, $value);
        }
        $order->save();
    }

    public function sync($order)
    {
        $this->syncCartWithOrder($order);
        $this->syncAdjustmentsWithOrder($order);
        $this->syncDataWithOrder($order);
    }

    private function getOrder($customer)
    {
        // create an order
        $order_class = getClassNameFor('order');

        $order = Cart::getActiveOrder() ?? $order_class::create([
            'customer_id' => $customer->id
        ]);

        return $order;
    }

    private function convertCartItemsToOrderItems($order)
    {
        $cart_items = Cart::items();

        $cart_items->each(function ($cart_item) use ($order) {
            //$product_key = getKeyFor('product');
            $order->items()->create([
                'name' => $cart_item->getProduct()->getName($cart_item->product_data),
                'product_id'=> $cart_item->product_id,
                'product_data' => $cart_item->product_data,
                'price' => $cart_item->getProduct()->getPrice($cart_item->product_data),
                'quantity' => $cart_item->quantity
            ]);
        });
    }

    private function setOrderData($order)
    {
        //saving additional cart/order data
        //@todo possibly expand to allow "hooks" or events to trigger methods depending on keys/values set?
        //$cart_data = $this->getData();

        collect($this->getData())->each(function($value, $key) use ($order) {
            $order->setData($key, $value);
        });

    }

    private function convertAdjustments($order)
    {
        //@todo convert cart adjustments to order adjustments
        //throw new \Exception('convert cart adjustments to order adjustments in Cart::createOrder');
        $this->getValidAdjustments(true)->concat($this->getValidAdjustments(false))->each(function($adjustment) use ($order) {
        //foreach($this->getValidAdjustments(true)->concat($this->getValidAdjustments(false)) as $adjustment) {
            config('laravel-cart.classes.order_adjustment')::create([
                'name' => $adjustment->name,
                'original_parameters' => $adjustment->parameters,
                'order_id' => $order->id,
                'class' => $adjustment->class,
                'amount' => $adjustment->calculated_amount,
                'apply_to_subtotal' => $adjustment->apply_to_subtotal
            ]);
        });
    }

    public function setActiveOrder(int|Order|null $order)
    {
        if(is_null($order)) {
            session()->remove('active_order');
        } else if(is_int($order)) {
            session()->put('active_order', $order);
        } else {
            session()->put('active_order', $order->id);
        }
    }

    public function getActiveOrder()
    {
        $order_id =  session()->get('active_order');
        $order_class = getClassNameFor('order');
        return $order_class::where('id', $order_id)->first();
    }

    public function addData($key, $value)
    {
        $cart_data = session()->get('cart_data') ?? [];
        $cart_data[$key] = $value;
        session()->put('cart_data', $cart_data);
    }

    public function getData($key = null)
    {
        $cart_data = session()->get('cart_data') ?? [];

        if($key)
        {
            return $cart_data[$key] ?? '';
        }

        return $cart_data;
    }
}
