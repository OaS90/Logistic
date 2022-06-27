<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'regions';
    public $timestamps = false;

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'region_id', 'id');
    }
}
