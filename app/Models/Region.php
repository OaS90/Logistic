<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Region extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    protected $table = 'regions';
    protected $fillable = ['name', 'region_id', 'is_active_for_quotes'];
    public $timestamps = false;

    public function warehouse(): BelongsToMany
    {
        return $this->BelongsToMany(QuoteWarehouse::class, 'quote_warehouse_region', 'region_id', 'quote_warehouse_id');
    }

    public function getWarehouseNameAttribute(): string
    {
        return $this->warehouse->first()->warehouse_name;
    }
}
