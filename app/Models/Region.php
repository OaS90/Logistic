<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function tariffCategories(): BelongsToMany
    {
        return $this->belongsToMany(TariffCategories::class, 'tariff_category_region','region_id', 'category_id');
    }

    public function tariffCategorySettings(): BelongsToMany
    {
        return $this->belongsToMany(TariffCategories::class, 'tariff_category_region_settings', 'region_id', 'category_id')
            ->withPivot(['tariff_id', 'is_use']);
    }
}
