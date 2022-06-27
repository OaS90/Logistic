<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use CrudTrait;

    protected $table = 'region_warehouse';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function regions()
    {
        return $this->hasMany(Region::class, 'region_id', 'id');
    }
}
