<?php

namespace App\Models\Hru;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
