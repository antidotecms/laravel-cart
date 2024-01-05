<?php

namespace Antidote\LaravelCart\Models;

use Antidote\LaravelCart\Database\Factories\OrderFactory;
use Antidote\LaravelCart\Models\Concerns\ConfiguresOrder;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|static query()
 * @method static static make(array $attributes = [])
 * @method static static create(array $attributes = [])
 * @method static static forceCreate(array $attributes)
 * @method static firstOrNew(array $attributes = [], array $values = [])
 * @method static firstOrFail($columns = ['*'])
 * @method static firstOrCreate(array $attributes, array $values = [])
 * @method static firstOr($columns = ['*'], \Closure $callback = null)
 * @method static firstWhere($column, $operator = null, $value = null, $boolean = 'and')
 * @method static updateOrCreate(array $attributes, array $values = [])
 * @method null|static first($columns = ['*'])
 * @method static static findOrFail($id, $columns = ['*'])
 * @method static static findOrNew($id, $columns = ['*'])
 * @method static null|static find($id, $columns = ['*'])
 * @property-read int $id
 *
 * @method static OrderFactory|Order factory(...$parameters)
 */

class Order extends Model
{
    use ConfiguresOrder;
    use HasFactory;
    protected static function newFactory()
    {
        return OrderFactory::new();
    }

    public function items() : HasMany
    {
        return $this->hasMany(getClassNameFor('order_item'), 'order_id');
    }

    public function adjustments() : HasMany
    {
        return $this->hasMany(getClassNameFor('order_adjustment'), 'order_id');
    }

    public function customer() : BelongsTo
    {
        return $this->belongsTo(getClassNameFor('customer'), 'customer_id');
    }

    public function subtotal(): Attribute
    {
        return Attribute::make(
            get: function($value) : int {
                $subtotal = 0;
                $this->items()->each(function ($order_item) use (&$subtotal) {
                    $subtotal += $order_item->getCost();
                });
                return $subtotal;
            });
    }

    public function total() : Attribute
    {
        return Attribute::make(
            get: function($value) : int {
                $total = $this->subtotal;
                $total += $this->getAdjustmentTotal(false);
                $total += $this->getAdjustmentTotal(true);
                $total += (int) $this->tax;
                return $total;
            }
        );
    }

    public function tax() : Attribute
    {
        return Attribute::make(
            get: function ($value) : int {
                return ceil(ceil(($this->subtotal + $this->getAdjustmentTotal(true)) * config('laravel-cart.tax_rate')) * 100)/100;
            }
        );
    }

    public function getAdjustmentTotal(bool $is_in_subtotal)
    {
        $adjustment_total = 0;

        $this->load('adjustments');

        $this->getAdjustments($is_in_subtotal)
            ->each(function(OrderAdjustment $adjustment) use (&$adjustment_total, $is_in_subtotal) {
                $adjustment_total += $adjustment->amount;
            });

        return $adjustment_total;
    }

    //@todo can this be rolled into the relationship?
    public function getAdjustments(bool $is_in_subtotal)
    {
        return $this->adjustments->when($is_in_subtotal,
            fn($query) => $query->appliedToSubtotal(),
            fn($query) => $query->appliedToTotal()
        );
    }

    public function logItems() : hasMany
    {
        return $this->hasMany(CartPanelPlugin::get('models.order_log_item'), 'order_id');
    }

    public function log(string $message) : OrderLogItem
    {
        return $this->logItems()->create([
            'message' => $message
        ]);
    }

    //@tdo should this return an exception to inform developer that it must be overridden in their own class
//    public function updateStatus()
//    {
//        //throw new \Exception('Order should be overriden and implement updateStatus');
//        (new $this->payment->payment_method_type)->updateStatus();
//    }

    //@tdo should this return an exception to inform developer that it must be overridden in their own class
//    public function isCompleted()
//    {
//        //throw new \Exception('Order should be overriden and implement isCompleted');
//        (new $this->payment->payment_method_type)->isCompleted();
//    }

    public function data()
    {
        return $this->hasMany(OrderData::class, 'order_id');
    }

    public function setData($key, $value)
    {
        $this->data()->updateOrCreate(
            ['key' => $key],
            ['value' => json_encode($value)]
        );
    }

    public function getData($key): null | string | array
    {
        if($data = $this->data()->where('key', $key)->first()) {
            return json_decode($data->value, JSON_OBJECT_AS_ARRAY);
        }

        return null;
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
