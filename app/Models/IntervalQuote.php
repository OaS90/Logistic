<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\LogsActivity;

class IntervalQuote extends Model
{
    use LogsActivity;

    protected $table = 'interval_quote';
    protected $guarded = ['id'];
}
