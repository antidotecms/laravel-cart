<?php

namespace Antidote\LaravelCart\Models;

use Antidote\LaravelCart\Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'line_1',
        'line_2',
        'town_city',
        'county',
        'postcode',
    ];

    public static function factory($count = null, $state = [])
    {
        return AddressFactory::new();
    }
}
