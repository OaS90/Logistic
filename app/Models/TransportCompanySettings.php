<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportCompanySettings extends Model
{
    use HasFactory;

    protected $table = 'transport_company_settings';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
    protected $casts = [
        'days' => 'array'
    ];

    public function tc(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TransportCompany::class, 'id', 'tc_id');
    }

    public function warehouse(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TransportCompanyWarehouse::class, 'tc_warehouse_id', 'id');
    }
}
