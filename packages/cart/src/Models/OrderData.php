<?php

namespace Antidote\LaravelCart\Models;

use Antidote\LaravelCart\Database\Factories\OrderDataFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderData extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value'
    ];

    protected static function newFactory()
    {
        return OrderDataFactory::new();
    }

    public function order()
    {
        return $this->belongsTo(getClassNameFor('order'));
    }
}
