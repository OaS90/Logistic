<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTariffPermission extends Model
{
    protected $table = 'admin_user_tariff_permissions';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}