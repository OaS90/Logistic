<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    protected $table = 'regions';
    protected $fillable = ['name', 'region_id', 'warehouse_id'];
    public $timestamps = false;

    public function warehouse()
    {
        return $this->belongsTo(QuoteWarehouse::class);
    }

    public function getWarehouseName()
    {
        return $this->warehouse->warehouse_name;
    }
}
