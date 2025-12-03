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

class Filial extends Model
{
    use HasFactory, CrudTrait, LogsActivity;

    /**
     * @inheritdoc
     */
    protected $table = 'hru_filials';

    /**
     * @inheritdoc
     */
    protected $primaryKey = 'id';

    /**
     * @inheritdoc
     */
    protected $guarded = ['id'];

    public function getFilialCodeAttribute(): string
    {
        return sprintf('%05d', $this->filial_id);
    }

    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'hru_warehouse_filial', 'filial_id', 'warehouse_id')
            ->withPivot(['is_virtual']);
    }

    public function tcSettings(): HasMany
    {
        return $this->hasMany(TransportCompanySettings::class, 'filial_id', 'id');
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
