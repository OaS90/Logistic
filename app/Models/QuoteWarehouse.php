<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteWarehouse extends Model
{
    use CrudTrait;

    protected $table = 'quote_warehouse';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function regions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Region::class, 'warehouse_id', 'id');
    }
}
