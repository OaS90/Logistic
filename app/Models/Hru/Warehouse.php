<?php

namespace App\Models\Hru;

use App\Models\Region;
use App\Models\TransportCompanySettings;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\LogsActivity;

class Warehouse extends Model
{
    use CrudTrait, HasFactory, LogsActivity;

    /**
     * @inheritdoc
     */
    protected $table = 'hru_warehouses';

    /**
     * @inheritdoc
     */
    protected $primaryKey = 'id';

    /**
     * @inheritdoc
     */
    protected $guarded = ['id'];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_id', 'id');
    }

    public function tcSettings(): HasMany
    {
        return $this->hasMany(TransportCompanySettings::class, 'tc_warehouse_id', 'id');
    }

    public function getRegionName(): ?string
    {
        return $this->region->name;
    }

    public function filials(): BelongsToMany
    {
        return $this->belongsToMany(Filial::class, 'hru_warehouse_filial', 'warehouse_id', 'filial_id');
    }
}
