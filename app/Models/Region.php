<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Region extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;

    protected $table = 'regions';
    protected $fillable = ['name', 'region_id', 'is_active_for_quotes'];
    public $timestamps = false;

    public function warehouses(): HasMany
    {
        return $this->hasMany(\App\Models\Hru\Warehouse::class, 'region_id', 'id');
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
