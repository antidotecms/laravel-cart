<?php

namespace Antidote\LaravelCart\Models;

use Antidote\LaravelCart\Contracts\PaymentManager;
use Antidote\LaravelCart\Enums\PaymentMethod;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $payment_method_type
 * @property Order $order
 */

class Payment extends Model
{
    protected $fillable = [
        'payment_method_type'
    ];

    protected $casts = [
        'payment_method_type' =>  PaymentMethod::class
    ];

    public function order()
    {
        return $this->belongsTo(CartPanelPlugin::get('models.order'));
    }

    public function isCompleted()
    {
        return $this->manager()->isCompleted($this->order);
    }

    public function manager(): PaymentManager
    {
        return $this->payment_method_type->manager();
    }
}
