<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
    protected $table = 'delivery_addresses';
    protected $guarded = ['id'];
    public $timestamps = false;
}
