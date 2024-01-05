<?php

namespace Antidote\LaravelCart\Models;

use Antidote\LaravelCartFilament\CartPanelPlugin;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'payment_method_type'
    ];

    public function order()
    {
        return $this->belongsTo(CartPanelPlugin::get('models.order'));
    }

    public function isCompleted()
    {
        return (new $this->payment_method_type)->isCompleted($this->order);
    }
}
