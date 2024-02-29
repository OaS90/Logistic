<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TariffRegionCategorySetting extends Model
{
    use HasFactory;

    protected $table = 'tariff_region_category_settings';
    public $timestamps = false;
    protected $fillable = ['tariff_id', 'region_id', 'category_id', 'is_use'];
}
