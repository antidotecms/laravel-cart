<?php

namespace Antidote\LaravelCartStripe\Models;

use Antidote\LaravelCartStripe\Database\factories\StripeOrderFactory;
use Antidote\LaravelCartStripe\Domain\PaymentIntent;

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
 *
 * @property-read int $id
 *
 * @method static StripeOrderFactory|StripeOrder factory(...$parameters)
 */

class StripeOrder extends \Antidote\LaravelCart\Models\Order
{
    protected static function newFactory()
    {
        return StripeOrderFactory::new();
    }

    public function updateStatus()
    {
        $payment_intent = app(PaymentIntent::class);
        $payment_intent->retrieveStatus($this);
    }

    public function isCompleted()
    {
        return $this->status == 'succeeded';
    }
}
