<?php

namespace App\Models\Hru;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Filial extends Model
{
    use HasFactory;

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

    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'hru_warehouse_filial', 'filial_id', 'warehouse_id');
    }
}
