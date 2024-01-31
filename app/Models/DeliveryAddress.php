<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
    protected $table = 'delivery_addresses';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function getRegionAndCityAttribute(): string
    {
        return $this->city_name . ', ' . $this->region_name;
    }

    public function getFullAddressAttribute(): string
    {
        return $this->city_name . ', ' . $this->region_name . ', ' . $this->street . ', д.' . $this->building . ', кв. ' . $this->flast;
    }
}
