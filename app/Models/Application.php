<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'applications';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(DeliveryAddress::class, 'delivery_address', 'id');
    }

    public function getFullAddressAttribute()
    {
        return implode(', ', [
            $this->address->city_name,
            $this->address->region_name,
            $this->address->street,
            $this->address->building,
            $this->address->entrance ? 'п. ' . $this->address->entrance : '',
            $this->address->floor ? 'этаж ' . $this->address->floor : '',
            $this->address->flat ? 'кв. ' . $this->address->flat : ''
        ]);
    }
}
