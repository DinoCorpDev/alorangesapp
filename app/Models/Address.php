<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Address extends Model
{
    protected $fillable = [
        'address',
        'user_id',
        'name',
        'details',
        'country',
        'country_id',
        'state',
        'localidad_id',
        'state_id',
        'city',
        'city_id',
        'neighborhood',
        'postal_code',
        'phone',
        'default_shipping',
        'default_billing',
        'default_service',
        'favorite',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function localidad()
    {
        return $this->belongsTo(Localidad::class, 'localidad_id');
    }
}
