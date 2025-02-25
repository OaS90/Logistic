<?php

namespace App\Models\Hru;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

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
}
