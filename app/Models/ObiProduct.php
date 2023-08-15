<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObiProduct extends Model
{
    protected $table = 'application_obi_products';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
    public $timestamps = false;
}