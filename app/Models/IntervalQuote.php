<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class IntervalQuote extends Model
{
    protected $table = 'interval_quote';
    protected $guarded = ['id'];
}
