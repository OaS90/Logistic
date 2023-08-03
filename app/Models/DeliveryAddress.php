<?php

namespace App\Models;

use App\Shared\Eloquent\ConvertsToUtfTrait;
use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
    use ConvertsToUtfTrait;

    protected $table = 'delivery_addresses';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function getRegionAndCityAttribute(): string
    {
        return $this->city_name . ', ' . $this->region_name;
    }
}
