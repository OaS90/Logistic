<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\LogsActivity;

class TariffRegionCategorySetting extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'tariff_region_category_settings';
    public $timestamps = false;
    protected $primaryKey = null;
    public $incrementing = false;
    protected $fillable = ['tariff_id', 'region_id', 'category_id', 'is_use'];
}
