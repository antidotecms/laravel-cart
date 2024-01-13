<?php

namespace Antidote\LaravelCart\Models;

use Antidote\LaravelCart\Database\Factories\OrderDataFactory;
use Antidote\LaravelCart\Database\Factories\PaymentDataFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentData extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value'
    ];

    protected static function newFactory()
    {
        return PaymentDataFactory::new();
    }

    public function order()
    {
        return $this->belongsTo(getClassNameFor('order'));
    }
}
