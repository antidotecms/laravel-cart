<?php

namespace Antidote\LaravelCart\Models;

use Antidote\LaravelCart\Database\Factories\CustomerFactory;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory;
    use Notifiable, MustVerifyEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getForeignKey()
    {
        return 'customer_id';
    }

    protected static function newFactory()
    {
        return CustomerFactory::new();
    }

    public function getTable()
    {
        return 'customers';
    }

    public function orders() : hasMany
    {
        return $this->hasMany(getClassNameFor('order'));
    }
}
