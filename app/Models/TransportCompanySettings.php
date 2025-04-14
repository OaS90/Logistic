<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Hru\Warehouse;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransportCompanySettings extends Model
{
    use HasFactory;

    protected $table = 'transport_company_settings';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
    protected $casts = [
        'days' => 'array'
    ];
    protected array $daysName = [
        1 => 'Пн',
        2 => 'Вт',
        3 => 'Ср',
        4 => 'Чт',
        5 => 'Пт',
        6 => 'Сб',
        7 => 'Вс'
    ];
    public function tc(): HasOne
    {
        return $this->hasOne(TransportCompany::class, 'id', 'tc_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'tc_warehouse_id', 'id');
    }

    public function getDaysNameAttribute(): string
    {
        $data = [];

        if ($this->days) {
            foreach ($this->days as $dayNumber => $enabled) {
                if ($enabled) {
                    $data[] = $this->daysName[$dayNumber];
                }
            }

            return implode(', ', $data);
        }

        return '';
    }

    public function filial()
    {
        return $this->belongsTo(Filial::class, 'filial_id', 'id');
    }
}
