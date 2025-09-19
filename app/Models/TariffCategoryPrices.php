<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\LogsActivity;

class TariffCategoryPrices extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'tariff_region_category_prices';
    protected $guarded = ['id'];
    protected $primaryKey = 'id';

    public function zone(): BelongsTo
    {
        return $this->belongsTo(TariffRegionZone::class, 'zone_id', 'id');
    }
}
